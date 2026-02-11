<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { z } from 'zod'
import type { FormSubmitEvent } from '@nuxt/ui'

const toast = useToast()

const schema = z.object({
  nombre: z.string().min(1, 'El nombre es obligatorio'),
  descripcion: z.string().optional(),
  activo: z.boolean().default(true)
})

type Schema = z.output<typeof schema>

const state = ref<Partial<Schema>>({
  nombre: '',
  descripcion: '',
  activo: true
})

const loading = ref(false)

async function onSubmit(event: FormSubmitEvent<Schema>) {
  loading.value = true

  router.post(route('categorias.store'), event.data, {
    onSuccess: () => {
      toast.add({ title: 'Categoria creada correctamente', color: 'success' })
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0]
      toast.add({ title: 'Error', description: firstError as string, color: 'error' })
    },
    onFinish: () => { loading.value = false }
  })
}

const goBack = () => router.visit(route('categorias.index'))
</script>

<template>
  <UDashboardPanel id="categorias-create">
    <template #header>
      <UDashboardNavbar title="Nueva Categoria">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold">Nueva Categoria</h2>
          <p class="text-sm text-muted mt-1">Complete los datos de la categoria</p>
        </div>

        <UForm :schema="schema" :state="state" class="space-y-6 max-w-2xl" @submit="onSubmit">
          <UFormField label="Nombre" name="nombre" size="xl" class="w-full">
            <UInput v-model="state.nombre" placeholder="Ej: Pantallas, Baterias..." size="xl" class="w-full" />
          </UFormField>

          <UFormField label="Descripcion" name="descripcion" size="xl" class="w-full">
            <UTextarea v-model="state.descripcion" placeholder="Descripcion de la categoria..." size="xl" class="w-full" />
          </UFormField>

          <UFormField label="Estado" name="activo" size="xl" class="w-full">
            <UCheckbox v-model="state.activo" label="Activa" />
          </UFormField>

          <div class="flex justify-end gap-3 pt-4">
            <UButton type="submit" label="Guardar Categoria" color="primary" :loading="loading" icon="i-lucide-save" />
            <UButton label="Cancelar" color="neutral" variant="outline" @click="goBack" />
          </div>
        </UForm>
      </div>
    </template>
  </UDashboardPanel>
</template>
