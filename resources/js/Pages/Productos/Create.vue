<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { z } from 'zod'
import type { FormSubmitEvent } from '@nuxt/ui'

interface Categoria {
  id: string
  nombre: string
}

const props = defineProps<{
  categorias: { data: Categoria[] }
}>()

const toast = useToast()

const schema = z.object({
  categoriaId: z.string().min(1, 'La categoria es obligatoria'),
  codigo: z.string().min(1, 'El codigo es obligatorio'),
  nombre: z.string().min(1, 'El nombre es obligatorio'),
  descripcion: z.string().optional(),
  precioUnitario: z.number().min(0, 'El precio no puede ser negativo'),
  stock: z.number().min(0, 'El stock no puede ser negativo'),
  tipo: z.enum(['bien', 'servicio'], { required_error: 'El tipo es obligatorio' }),
  activo: z.boolean().default(true)
})

type Schema = z.output<typeof schema>

const state = ref<Partial<Schema>>({
  categoriaId: '',
  codigo: '',
  nombre: '',
  descripcion: '',
  precioUnitario: 0,
  stock: 0,
  tipo: 'bien',
  activo: true
})

const loading = ref(false)

const categoriaItems = computed(() =>
  props.categorias.data.map(c => ({ label: c.nombre, value: c.id }))
)

const tipoItems = [
  { label: 'Bien', value: 'bien' },
  { label: 'Servicio', value: 'servicio' }
]

async function onSubmit(event: FormSubmitEvent<Schema>) {
  loading.value = true

  router.post(route('productos.store'), event.data, {
    onSuccess: () => {
      toast.add({ title: 'Producto creado correctamente', color: 'success' })
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0]
      toast.add({ title: 'Error', description: firstError as string, color: 'error' })
    },
    onFinish: () => { loading.value = false }
  })
}

const goBack = () => router.visit(route('productos.index'))
</script>

<template>
  <UDashboardPanel id="productos-create">
    <template #header>
      <UDashboardNavbar title="Nuevo Producto">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold">Nuevo Producto</h2>
          <p class="text-sm text-muted mt-1">Complete los datos del producto</p>
        </div>

        <UForm :schema="schema" :state="state" class="space-y-6 max-w-2xl" @submit="onSubmit">
          <div class="grid grid-cols-2 gap-8">
            <UFormField label="Codigo" name="codigo" size="xl" class="w-full">
              <UInput v-model="state.codigo" placeholder="PROD-001" size="xl" class="w-full" />
            </UFormField>

            <UFormField label="Nombre" name="nombre" size="xl" class="w-full">
              <UInput v-model="state.nombre" placeholder="Nombre del producto" size="xl" class="w-full" />
            </UFormField>

            <UFormField label="Categoria" name="categoriaId" size="xl" class="w-full">
              <USelect v-model="state.categoriaId" :items="categoriaItems" value-key="value" placeholder="Seleccionar categoria" size="xl" class="w-full" />
            </UFormField>

            <UFormField label="Tipo" name="tipo" size="xl" class="w-full">
              <USelect v-model="state.tipo" :items="tipoItems" value-key="value" size="xl" class="w-full" />
            </UFormField>
          </div>

          <UFormField label="Descripcion" name="descripcion" size="xl" class="w-full">
            <UTextarea v-model="state.descripcion" placeholder="Descripcion del producto..." size="xl" class="w-full" />
          </UFormField>

          <div class="grid grid-cols-2 gap-8">
            <UFormField label="Precio unitario" name="precioUnitario" size="xl" class="w-full">
              <UInput v-model.number="state.precioUnitario" type="number" step="0.01" min="0" size="xl" class="w-full" />
            </UFormField>

            <UFormField label="Stock" name="stock" size="xl" class="w-full">
              <UInput v-model.number="state.stock" type="number" min="0" size="xl" class="w-full" />
            </UFormField>
          </div>

          <UFormField label="Estado" name="activo" size="xl" class="w-full">
            <UCheckbox v-model="state.activo" label="Activo" />
          </UFormField>

          <div class="flex justify-end gap-3 pt-4">
            <UButton type="submit" label="Guardar Producto" color="primary" :loading="loading" icon="i-lucide-save" />
            <UButton label="Cancelar" color="neutral" variant="outline" @click="goBack" />
          </div>
        </UForm>
      </div>
    </template>
  </UDashboardPanel>
</template>
