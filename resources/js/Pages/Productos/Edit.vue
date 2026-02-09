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

interface Producto {
  id: string
  categoriaId: string
  codigo: string
  nombre: string
  descripcion?: string
  precioUnitario: number
  stock: number
  tipo: 'bien' | 'servicio'
  activo: boolean
  categoria?: { id: string; nombre: string }
}

const props = defineProps<{
  producto: { data: Producto }
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
  activo: z.boolean()
})

type Schema = z.output<typeof schema>

const state = ref<Partial<Schema>>({
  categoriaId: props.producto.data.categoriaId,
  codigo: props.producto.data.codigo,
  nombre: props.producto.data.nombre,
  descripcion: props.producto.data.descripcion || '',
  precioUnitario: Number(props.producto.data.precioUnitario),
  stock: props.producto.data.stock,
  tipo: props.producto.data.tipo,
  activo: props.producto.data.activo
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
  try {
    const payload = {
      ...event.data,
      categoria_id: event.data.categoriaId,
      precio_unitario: event.data.precioUnitario
    }

    const response = await fetch(route('api.productos.update', props.producto.data.id), {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1] || '')
      },
      credentials: 'include',
      body: JSON.stringify(payload)
    })

    if (response.ok) {
      toast.add({ title: 'Producto actualizado', color: 'success' })
      router.visit(route('productos.index'))
    } else {
      const error = await response.json()
      toast.add({ title: 'Error', description: error.message, color: 'error' })
    }
  } finally {
    loading.value = false
  }
}

const goBack = () => router.visit(route('productos.index'))
</script>

<template>
  <UDashboardPanel id="productos-edit">
    <template #header>
      <UDashboardNavbar title="Editar Producto">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <UForm :schema="schema" :state="state" class="space-y-6 max-w-2xl" @submit="onSubmit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <UFormField label="Codigo" name="codigo">
            <UInput v-model="state.codigo" />
          </UFormField>

          <UFormField label="Nombre" name="nombre">
            <UInput v-model="state.nombre" />
          </UFormField>

          <UFormField label="Categoria" name="categoriaId">
            <USelect v-model="state.categoriaId" :items="categoriaItems" value-key="value" placeholder="Seleccionar categoria" />
          </UFormField>

          <UFormField label="Tipo" name="tipo">
            <USelect v-model="state.tipo" :items="tipoItems" value-key="value" />
          </UFormField>
        </div>

        <UFormField label="Descripcion" name="descripcion">
          <UTextarea v-model="state.descripcion" />
        </UFormField>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <UFormField label="Precio unitario" name="precioUnitario">
            <UInput v-model.number="state.precioUnitario" type="number" step="0.01" min="0" />
          </UFormField>

          <UFormField label="Stock" name="stock">
            <UInput v-model.number="state.stock" type="number" min="0" />
          </UFormField>
        </div>

        <UFormField label="Estado" name="activo">
          <UCheckbox v-model="state.activo" label="Activo" />
        </UFormField>

        <div class="flex gap-3">
          <UButton type="submit" label="Actualizar" color="primary" :loading="loading" />
          <UButton label="Cancelar" color="neutral" variant="subtle" @click="goBack" />
        </div>
      </UForm>
    </template>
  </UDashboardPanel>
</template>
