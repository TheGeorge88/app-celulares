<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { z } from 'zod'
import type { FormSubmitEvent } from '@nuxt/ui'

interface Cliente { id: string; razonSocial: string; numeroDocumento: string }
interface Equipo {
  id: string; clienteId: string; marca: string; modelo: string
  imei: string; color: string; observaciones?: string
}

const props = defineProps<{
  equipo: { data: Equipo }
  clientes: { data: Cliente[] }
}>()

const toast = useToast()

const schema = z.object({
  clienteId: z.string().min(1, 'Seleccione un cliente'),
  marca: z.string().min(1, 'La marca es obligatoria'),
  modelo: z.string().min(1, 'El modelo es obligatorio'),
  imei: z.string().min(1, 'El IMEI es obligatorio'),
  color: z.string().min(1, 'El color es obligatorio'),
  observaciones: z.string().optional()
})

type Schema = z.output<typeof schema>

const state = ref<Partial<Schema>>({
  clienteId: props.equipo.data.clienteId,
  marca: props.equipo.data.marca,
  modelo: props.equipo.data.modelo,
  imei: props.equipo.data.imei,
  color: props.equipo.data.color,
  observaciones: props.equipo.data.observaciones || ''
})

const loading = ref(false)
const clienteOptions = computed(() =>
  props.clientes.data.map(c => ({ label: `${c.razonSocial} - ${c.numeroDocumento}`, value: c.id }))
)

async function onSubmit(event: FormSubmitEvent<Schema>) {
  loading.value = true
  try {
    const response = await fetch(route('api.equipos.update', props.equipo.data.id), {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1] || '')
      },
      credentials: 'include',
      body: JSON.stringify(event.data)
    })

    if (response.ok) {
      toast.add({ title: 'Equipo actualizado', color: 'success' })
      router.visit(route('equipos.index'))
    } else {
      const error = await response.json()
      toast.add({ title: 'Error', description: error.message, color: 'error' })
    }
  } finally {
    loading.value = false
  }
}

const goBack = () => router.visit(route('equipos.index'))
</script>

<template>
  <UDashboardPanel id="equipos-edit">
    <template #header>
      <UDashboardNavbar title="Editar Equipo">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <UForm :schema="schema" :state="state" class="space-y-6 max-w-2xl" @submit="onSubmit">
        <UFormField label="Cliente" name="clienteId">
          <USelectMenu v-model="state.clienteId" :items="clienteOptions" />
        </UFormField>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <UFormField label="Marca" name="marca">
            <UInput v-model="state.marca" />
          </UFormField>
          <UFormField label="Modelo" name="modelo">
            <UInput v-model="state.modelo" />
          </UFormField>
          <UFormField label="IMEI" name="imei">
            <UInput v-model="state.imei" />
          </UFormField>
          <UFormField label="Color" name="color">
            <UInput v-model="state.color" />
          </UFormField>
        </div>

        <UFormField label="Observaciones" name="observaciones">
          <UTextarea v-model="state.observaciones" />
        </UFormField>

        <div class="flex gap-3">
          <UButton type="submit" label="Actualizar" color="primary" :loading="loading" />
          <UButton label="Cancelar" color="neutral" variant="subtle" @click="goBack" />
        </div>
      </UForm>
    </template>
  </UDashboardPanel>
</template>
