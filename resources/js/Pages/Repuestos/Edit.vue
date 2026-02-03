<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { z } from 'zod'
import type { FormSubmitEvent } from '@nuxt/ui'

interface Repuesto {
  id: string; codigo: string; nombre: string; descripcion?: string
  marca: string; modelo: string; stock: number; stockMinimo: number
  precioCompra: number; precioVenta: number; activo: boolean
}

const props = defineProps<{
  repuesto: { data: Repuesto }
}>()

const toast = useToast()

const schema = z.object({
  codigo: z.string().min(1),
  nombre: z.string().min(1),
  descripcion: z.string().optional(),
  marca: z.string().min(1),
  modelo: z.string().min(1),
  stock: z.number().min(0),
  stockMinimo: z.number().min(0),
  precioCompra: z.number().min(0),
  precioVenta: z.number().min(0),
  activo: z.boolean()
})

type Schema = z.output<typeof schema>

const state = ref<Partial<Schema>>({
  codigo: props.repuesto.data.codigo,
  nombre: props.repuesto.data.nombre,
  descripcion: props.repuesto.data.descripcion || '',
  marca: props.repuesto.data.marca,
  modelo: props.repuesto.data.modelo,
  stock: props.repuesto.data.stock,
  stockMinimo: props.repuesto.data.stockMinimo,
  precioCompra: props.repuesto.data.precioCompra,
  precioVenta: props.repuesto.data.precioVenta,
  activo: props.repuesto.data.activo
})

const loading = ref(false)

async function onSubmit(event: FormSubmitEvent<Schema>) {
  loading.value = true
  try {
    const response = await fetch(route('api.repuestos.update', props.repuesto.data.id), {
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
      toast.add({ title: 'Repuesto actualizado', color: 'success' })
      router.visit(route('repuestos.index'))
    } else {
      const error = await response.json()
      toast.add({ title: 'Error', description: error.message, color: 'error' })
    }
  } finally {
    loading.value = false
  }
}

const goBack = () => router.visit(route('repuestos.index'))
</script>

<template>
  <UDashboardPanel id="repuestos-edit">
    <template #header>
      <UDashboardNavbar title="Editar Repuesto">
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
          <UFormField label="Marca" name="marca">
            <UInput v-model="state.marca" />
          </UFormField>
          <UFormField label="Modelo" name="modelo">
            <UInput v-model="state.modelo" />
          </UFormField>
        </div>

        <UFormField label="Descripcion" name="descripcion">
          <UTextarea v-model="state.descripcion" />
        </UFormField>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <UFormField label="Stock" name="stock">
            <UInput v-model.number="state.stock" type="number" min="0" />
          </UFormField>
          <UFormField label="Stock minimo" name="stockMinimo">
            <UInput v-model.number="state.stockMinimo" type="number" min="0" />
          </UFormField>
          <UFormField label="Precio compra" name="precioCompra">
            <UInput v-model.number="state.precioCompra" type="number" step="0.01" min="0" />
          </UFormField>
          <UFormField label="Precio venta" name="precioVenta">
            <UInput v-model.number="state.precioVenta" type="number" step="0.01" min="0" />
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
