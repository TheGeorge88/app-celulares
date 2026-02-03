<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { z } from 'zod'
import type { FormSubmitEvent } from '@nuxt/ui'

const toast = useToast()

const schema = z.object({
  cedula: z.string().min(1, 'La cedula es obligatoria'),
  nombre: z.string().min(1, 'El nombre es obligatorio'),
  apellido: z.string().min(1, 'El apellido es obligatorio'),
  telefono: z.string().min(1, 'El telefono es obligatorio'),
  email: z.string().email('Email invalido'),
  especialidad: z.string().min(1, 'La especialidad es obligatoria'),
  activo: z.boolean().default(true)
})

type Schema = z.output<typeof schema>

const state = ref<Partial<Schema>>({
  cedula: '',
  nombre: '',
  apellido: '',
  telefono: '',
  email: '',
  especialidad: '',
  activo: true
})

const loading = ref(false)

async function onSubmit(event: FormSubmitEvent<Schema>) {
  loading.value = true
  try {
    const response = await fetch(route('api.tecnicos.store'), {
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
      toast.add({ title: 'Tecnico creado correctamente', color: 'success' })
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
  <UDashboardPanel id="tecnicos-create">
    <template #header>
      <UDashboardNavbar title="Nuevo Tecnico">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <UForm :schema="schema" :state="state" class="space-y-6 max-w-2xl" @submit="onSubmit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <UFormField label="Cedula" name="cedula">
            <UInput v-model="state.cedula" placeholder="1234567890" />
          </UFormField>

          <UFormField label="Especialidad" name="especialidad">
            <UInput v-model="state.especialidad" placeholder="Pantallas, Baterias, etc." />
          </UFormField>

          <UFormField label="Nombre" name="nombre">
            <UInput v-model="state.nombre" placeholder="Juan" />
          </UFormField>

          <UFormField label="Apellido" name="apellido">
            <UInput v-model="state.apellido" placeholder="Perez" />
          </UFormField>

          <UFormField label="Email" name="email">
            <UInput v-model="state.email" type="email" placeholder="tecnico@email.com" />
          </UFormField>

          <UFormField label="Telefono" name="telefono">
            <UInput v-model="state.telefono" placeholder="0991234567" />
          </UFormField>

          <UFormField label="Estado" name="activo">
            <UCheckbox v-model="state.activo" label="Activo" />
          </UFormField>
        </div>

        <div class="flex gap-3">
          <UButton type="submit" label="Guardar" color="primary" :loading="loading" />
          <UButton label="Cancelar" color="neutral" variant="subtle" @click="goBack" />
        </div>
      </UForm>
    </template>
  </UDashboardPanel>
</template>
