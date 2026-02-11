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

  router.put(route('repuestos.update', props.repuesto.data.id), event.data, {
    onSuccess: () => {
      toast.add({ title: 'Repuesto actualizado', color: 'success' })
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0]
      toast.add({ title: 'Error', description: firstError as string, color: 'error' })
    },
    onFinish: () => { loading.value = false }
  })
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
      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold">Editar Repuesto</h2>
          <p class="text-sm text-muted mt-1">Modifique los datos del repuesto</p>
        </div>

        <UForm :schema="schema" :state="state" class="space-y-6 max-w-2xl" @submit="onSubmit">
          <div class="grid grid-cols-2 gap-8">
            <UFormField label="Codigo" name="codigo" size="xl" class="w-full">
              <UInput v-model="state.codigo" size="xl" class="w-full" />
            </UFormField>
            <UFormField label="Nombre" name="nombre" size="xl" class="w-full">
              <UInput v-model="state.nombre" size="xl" class="w-full" />
            </UFormField>
            <UFormField label="Marca" name="marca" size="xl" class="w-full">
              <UInput v-model="state.marca" size="xl" class="w-full" />
            </UFormField>
            <UFormField label="Modelo" name="modelo" size="xl" class="w-full">
              <UInput v-model="state.modelo" size="xl" class="w-full" />
            </UFormField>
          </div>

          <UFormField label="Descripcion" name="descripcion" size="xl" class="w-full">
            <UTextarea v-model="state.descripcion" size="xl" class="w-full" />
          </UFormField>

          <div class="grid grid-cols-2 gap-8">
            <UFormField label="Stock" name="stock" size="xl" class="w-full">
              <UInput v-model.number="state.stock" type="number" min="0" size="xl" class="w-full" />
            </UFormField>
            <UFormField label="Stock minimo" name="stockMinimo" size="xl" class="w-full">
              <UInput v-model.number="state.stockMinimo" type="number" min="0" size="xl" class="w-full" />
            </UFormField>
            <UFormField label="Precio compra" name="precioCompra" size="xl" class="w-full">
              <UInput v-model.number="state.precioCompra" type="number" step="0.01" min="0" size="xl" class="w-full" />
            </UFormField>
            <UFormField label="Precio venta" name="precioVenta" size="xl" class="w-full">
              <UInput v-model.number="state.precioVenta" type="number" step="0.01" min="0" size="xl" class="w-full" />
            </UFormField>
          </div>

          <UFormField label="Estado" name="activo" size="xl" class="w-full">
            <UCheckbox v-model="state.activo" label="Activo" />
          </UFormField>

          <div class="flex justify-end gap-3 pt-4">
            <UButton type="submit" label="Actualizar Repuesto" color="primary" :loading="loading" icon="i-lucide-save" />
            <UButton label="Cancelar" color="neutral" variant="outline" @click="goBack" />
          </div>
        </UForm>
      </div>
    </template>
  </UDashboardPanel>
</template>
