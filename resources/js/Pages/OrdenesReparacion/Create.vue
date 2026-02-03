<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { z } from 'zod'
import type { FormSubmitEvent } from '@nuxt/ui'

interface Cliente { id: string; razonSocial: string; numeroDocumento: string }
interface Tecnico { id: string; nombreCompleto: string; especialidad: string }
interface Equipo { id: string; marca: string; modelo: string; imei: string; clienteId: string }

const props = defineProps<{
  clientes: { data: Cliente[] }
  tecnicos: { data: Tecnico[] }
}>()

const toast = useToast()

const schema = z.object({
  clienteId: z.string().min(1, 'Seleccione un cliente'),
  equipoId: z.string().min(1, 'Seleccione un equipo'),
  tecnicoId: z.string().optional(),
  problemaReportado: z.string().min(1, 'Describa el problema'),
  costoEstimado: z.number().optional(),
  observaciones: z.string().optional()
})

type Schema = z.output<typeof schema>

const state = ref<Partial<Schema>>({
  clienteId: '',
  equipoId: '',
  tecnicoId: '',
  problemaReportado: '',
  costoEstimado: undefined,
  observaciones: ''
})

const loading = ref(false)
const equiposCliente = ref<Equipo[]>([])

const clienteOptions = computed(() =>
  props.clientes.data.map(c => ({ label: `${c.razonSocial} - ${c.numeroDocumento}`, value: c.id }))
)

const tecnicoOptions = computed(() =>
  [{ label: 'Sin asignar', value: '' }, ...props.tecnicos.data.map(t => ({ label: `${t.nombreCompleto} - ${t.especialidad}`, value: t.id }))]
)

const equipoOptions = computed(() =>
  equiposCliente.value.map(e => ({ label: `${e.marca} ${e.modelo} - IMEI: ${e.imei}`, value: e.id }))
)

watch(() => state.value.clienteId, async (clienteId) => {
  if (clienteId) {
    const response = await fetch(route('api.equipos.por-cliente', clienteId), {
      headers: { 'Accept': 'application/json' },
      credentials: 'include'
    })
    if (response.ok) {
      const data = await response.json()
      equiposCliente.value = data.data || data
    }
  } else {
    equiposCliente.value = []
  }
  state.value.equipoId = ''
})

async function onSubmit(event: FormSubmitEvent<Schema>) {
  loading.value = true
  try {
    const response = await fetch(route('api.ordenes-reparacion.store'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1] || '')
      },
      credentials: 'include',
      body: JSON.stringify(event.data)
    })

    if (response.ok) {
      const orden = await response.json()
      toast.add({ title: 'Orden creada', description: `Codigo: ${orden.data?.codigoSeguimiento || orden.codigoSeguimiento}`, color: 'success' })
      router.visit(route('ordenes-reparacion.index'))
    } else {
      const error = await response.json()
      toast.add({ title: 'Error', description: error.message, color: 'error' })
    }
  } finally {
    loading.value = false
  }
}

const goBack = () => router.visit(route('ordenes-reparacion.index'))
</script>

<template>
  <UDashboardPanel id="ordenes-create">
    <template #header>
      <UDashboardNavbar title="Nueva Orden de Reparacion">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <UForm :schema="schema" :state="state" class="space-y-6 max-w-2xl" @submit="onSubmit">
        <UFormField label="Cliente" name="clienteId">
          <USelectMenu v-model="state.clienteId" :items="clienteOptions" placeholder="Seleccione un cliente" searchable />
        </UFormField>

        <UFormField label="Equipo" name="equipoId">
          <USelectMenu
            v-model="state.equipoId"
            :items="equipoOptions"
            placeholder="Seleccione un equipo del cliente"
            :disabled="!state.clienteId"
          />
        </UFormField>

        <UFormField label="Tecnico asignado (opcional)" name="tecnicoId">
          <USelectMenu v-model="state.tecnicoId" :items="tecnicoOptions" placeholder="Asignar tecnico" />
        </UFormField>

        <UFormField label="Problema reportado" name="problemaReportado">
          <UTextarea v-model="state.problemaReportado" placeholder="Describa el problema que presenta el equipo..." rows="4" />
        </UFormField>

        <UFormField label="Costo estimado (opcional)" name="costoEstimado">
          <UInput v-model.number="state.costoEstimado" type="number" step="0.01" placeholder="0.00" />
        </UFormField>

        <UFormField label="Observaciones" name="observaciones">
          <UTextarea v-model="state.observaciones" placeholder="Observaciones adicionales..." />
        </UFormField>

        <div class="flex gap-3">
          <UButton type="submit" label="Crear Orden" color="primary" :loading="loading" />
          <UButton label="Cancelar" color="neutral" variant="subtle" @click="goBack" />
        </div>
      </UForm>
    </template>
  </UDashboardPanel>
</template>
