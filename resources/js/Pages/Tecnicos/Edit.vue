<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { z } from 'zod'
import type { FormSubmitEvent } from '@nuxt/ui'

interface Tecnico {
  id: string
  cedula: string
  nombre: string
  apellido: string
  telefono: string
  email: string
  especialidad: string
  activo: boolean
}

const props = defineProps<{
  tecnico: { data: Tecnico }
}>()

const toast = useToast()

const schema = z.object({
  cedula: z.string().min(1, 'La cedula es obligatoria'),
  nombre: z.string().min(1, 'El nombre es obligatorio'),
  apellido: z.string().min(1, 'El apellido es obligatorio'),
  telefono: z.string().min(1, 'El telefono es obligatorio'),
  email: z.string().email('Email invalido'),
  especialidad: z.string().min(1, 'La especialidad es obligatoria'),
  activo: z.boolean()
})

type Schema = z.output<typeof schema>

const state = ref<Partial<Schema>>({
  cedula: props.tecnico.data.cedula,
  nombre: props.tecnico.data.nombre,
  apellido: props.tecnico.data.apellido,
  telefono: props.tecnico.data.telefono,
  email: props.tecnico.data.email,
  especialidad: props.tecnico.data.especialidad,
  activo: props.tecnico.data.activo
})

const loading = ref(false)

async function onSubmit(event: FormSubmitEvent<Schema>) {
  loading.value = true
  try {
    const response = await fetch(route('api.tecnicos.update', props.tecnico.data.id), {
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
      toast.add({ title: 'Tecnico actualizado correctamente', color: 'success' })
      router.visit(route('tecnicos.index'))
    } else {
      const error = await response.json()
      toast.add({ title: 'Error', description: error.message, color: 'error' })
    }
  } catch (error) {
    toast.add({ title: 'Error de conexion', color: 'error' })
  } finally {
    loading.value = false
  }
}

const goBack = () => router.visit(route('tecnicos.index'))
</script>

<template>
  <UDashboardPanel id="tecnicos-edit">
    <template #header>
      <UDashboardNavbar title="Editar Tecnico">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <UForm :schema="schema" :state="state" class="space-y-6 max-w-2xl" @submit="onSubmit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <UFormField label="Cedula" name="cedula">
            <UInput v-model="state.cedula" />
          </UFormField>

          <UFormField label="Especialidad" name="especialidad">
            <UInput v-model="state.especialidad" />
          </UFormField>

          <UFormField label="Nombre" name="nombre">
            <UInput v-model="state.nombre" />
          </UFormField>

          <UFormField label="Apellido" name="apellido">
            <UInput v-model="state.apellido" />
          </UFormField>

          <UFormField label="Email" name="email">
            <UInput v-model="state.email" type="email" />
          </UFormField>

          <UFormField label="Telefono" name="telefono">
            <UInput v-model="state.telefono" />
          </UFormField>

          <UFormField label="Estado" name="activo">
            <UCheckbox v-model="state.activo" label="Activo" />
          </UFormField>
        </div>

        <div class="flex gap-3">
          <UButton type="submit" label="Actualizar" color="primary" :loading="loading" />
          <UButton label="Cancelar" color="neutral" variant="subtle" @click="goBack" />
        </div>
      </UForm>
    </template>
  </UDashboardPanel>
</template>
