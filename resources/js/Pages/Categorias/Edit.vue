<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { z } from 'zod'
import type { FormSubmitEvent } from '@nuxt/ui'

interface Categoria {
  id: string
  nombre: string
  descripcion?: string
  activo: boolean
}

const props = defineProps<{
  categoria: { data: Categoria }
}>()

const toast = useToast()

const schema = z.object({
  nombre: z.string().min(1, 'El nombre es obligatorio'),
  descripcion: z.string().optional(),
  activo: z.boolean()
})

type Schema = z.output<typeof schema>

const state = ref<Partial<Schema>>({
  nombre: props.categoria.data.nombre,
  descripcion: props.categoria.data.descripcion || '',
  activo: props.categoria.data.activo
})

const loading = ref(false)

async function onSubmit(event: FormSubmitEvent<Schema>) {
  loading.value = true
  try {
    const response = await fetch(route('api.categorias.update', props.categoria.data.id), {
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
      toast.add({ title: 'Categoria actualizada', color: 'success' })
      router.visit(route('categorias.index'))
    } else {
      const error = await response.json()
      toast.add({ title: 'Error', description: error.message, color: 'error' })
    }
  } finally {
    loading.value = false
  }
}

const goBack = () => router.visit(route('categorias.index'))
</script>

<template>
  <UDashboardPanel id="categorias-edit">
    <template #header>
      <UDashboardNavbar title="Editar Categoria">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold">Editar Categoria</h2>
          <p class="text-sm text-muted mt-1">Modifique los datos de la categoria</p>
        </div>

        <UForm :schema="schema" :state="state" class="space-y-6 max-w-2xl" @submit="onSubmit">
          <UFormField label="Nombre" name="nombre" size="xl" class="w-full">
            <UInput v-model="state.nombre" size="xl" class="w-full" />
          </UFormField>

          <UFormField label="Descripcion" name="descripcion" size="xl" class="w-full">
            <UTextarea v-model="state.descripcion" size="xl" class="w-full" />
          </UFormField>

          <UFormField label="Estado" name="activo" size="xl" class="w-full">
            <UCheckbox v-model="state.activo" label="Activa" />
          </UFormField>

          <div class="flex justify-end gap-3 pt-4">
            <UButton type="submit" label="Actualizar Categoria" color="primary" :loading="loading" icon="i-lucide-save" />
            <UButton label="Cancelar" color="neutral" variant="outline" @click="goBack" />
          </div>
        </UForm>
      </div>
    </template>
  </UDashboardPanel>
</template>
