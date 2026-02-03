<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { z } from 'zod'
import type { FormSubmitEvent } from '@nuxt/ui'

const toast = useToast()

const schema = z.object({
  codigo: z.string().min(1, 'El codigo es obligatorio'),
  nombre: z.string().min(1, 'El nombre es obligatorio'),
  descripcion: z.string().optional(),
  marca: z.string().min(1, 'La marca es obligatoria'),
  modelo: z.string().min(1, 'El modelo es obligatorio'),
  stock: z.number().min(0, 'El stock no puede ser negativo'),
  stockMinimo: z.number().min(0, 'El stock minimo no puede ser negativo'),
  precioCompra: z.number().min(0, 'El precio no puede ser negativo'),
  precioVenta: z.number().min(0, 'El precio no puede ser negativo'),
  activo: z.boolean().default(true)
})

type Schema = z.output<typeof schema>

const state = ref<Partial<Schema>>({
  codigo: '',
  nombre: '',
  descripcion: '',
  marca: '',
  modelo: '',
  stock: 0,
  stockMinimo: 5,
  precioCompra: 0,
  precioVenta: 0,
  activo: true
})

const loading = ref(false)

async function onSubmit(event: FormSubmitEvent<Schema>) {
  loading.value = true
  try {
    const response = await fetch(route('api.repuestos.store'), {
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
      toast.add({ title: 'Repuesto creado correctamente', color: 'success' })
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
  <UDashboardPanel id="repuestos-create">
    <template #header>
      <UDashboardNavbar title="Nuevo Repuesto">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <UForm :schema="schema" :state="state" class="space-y-6 max-w-2xl" @submit="onSubmit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <UFormField label="Codigo" name="codigo">
            <UInput v-model="state.codigo" placeholder="REP-001" />
          </UFormField>

          <UFormField label="Nombre" name="nombre">
            <UInput v-model="state.nombre" placeholder="Pantalla LCD" />
          </UFormField>

          <UFormField label="Marca" name="marca">
            <UInput v-model="state.marca" placeholder="Samsung, iPhone..." />
          </UFormField>

          <UFormField label="Modelo compatible" name="modelo">
            <UInput v-model="state.modelo" placeholder="Galaxy S21, iPhone 13..." />
          </UFormField>
        </div>

        <UFormField label="Descripcion" name="descripcion">
          <UTextarea v-model="state.descripcion" placeholder="Descripcion del repuesto..." />
        </UFormField>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <UFormField label="Stock inicial" name="stock">
            <UInput v-model.number="state.stock" type="number" min="0" />
          </UFormField>

          <UFormField label="Stock minimo" name="stockMinimo">
            <UInput v-model.number="state.stockMinimo" type="number" min="0" />
          </UFormField>

          <UFormField label="Precio de compra" name="precioCompra">
            <UInput v-model.number="state.precioCompra" type="number" step="0.01" min="0" />
          </UFormField>

          <UFormField label="Precio de venta" name="precioVenta">
            <UInput v-model.number="state.precioVenta" type="number" step="0.01" min="0" />
          </UFormField>
        </div>

        <UFormField label="Estado" name="activo">
          <UCheckbox v-model="state.activo" label="Activo" />
        </UFormField>

        <div class="flex gap-3">
          <UButton type="submit" label="Guardar" color="primary" :loading="loading" />
          <UButton label="Cancelar" color="neutral" variant="subtle" @click="goBack" />
        </div>
      </UForm>
    </template>
  </UDashboardPanel>
</template>
