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

  router.post(route('repuestos.store'), event.data, {
    onSuccess: () => {
      toast.add({ title: 'Repuesto creado correctamente', color: 'success' })
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
  <UDashboardPanel id="repuestos-create">
    <template #header>
      <UDashboardNavbar title="Nuevo Repuesto">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold">Nuevo Repuesto</h2>
          <p class="text-sm text-muted mt-1">Complete los datos del repuesto</p>
        </div>

        <UForm :schema="schema" :state="state" class="space-y-6 max-w-2xl" @submit="onSubmit">
          <div class="grid grid-cols-2 gap-8">
            <UFormField label="Codigo" name="codigo" size="xl" class="w-full">
              <UInput v-model="state.codigo" placeholder="REP-001" size="xl" class="w-full" />
            </UFormField>

            <UFormField label="Nombre" name="nombre" size="xl" class="w-full">
              <UInput v-model="state.nombre" placeholder="Pantalla LCD" size="xl" class="w-full" />
            </UFormField>

            <UFormField label="Marca" name="marca" size="xl" class="w-full">
              <UInput v-model="state.marca" placeholder="Samsung, iPhone..." size="xl" class="w-full" />
            </UFormField>

            <UFormField label="Modelo compatible" name="modelo" size="xl" class="w-full">
              <UInput v-model="state.modelo" placeholder="Galaxy S21, iPhone 13..." size="xl" class="w-full" />
            </UFormField>
          </div>

          <UFormField label="Descripcion" name="descripcion" size="xl" class="w-full">
            <UTextarea v-model="state.descripcion" placeholder="Descripcion del repuesto..." size="xl" class="w-full" />
          </UFormField>

          <div class="grid grid-cols-2 gap-8">
            <UFormField label="Stock inicial" name="stock" size="xl" class="w-full">
              <UInput v-model.number="state.stock" type="number" min="0" size="xl" class="w-full" />
            </UFormField>

            <UFormField label="Stock minimo" name="stockMinimo" size="xl" class="w-full">
              <UInput v-model.number="state.stockMinimo" type="number" min="0" size="xl" class="w-full" />
            </UFormField>

            <UFormField label="Precio de compra" name="precioCompra" size="xl" class="w-full">
              <UInput v-model.number="state.precioCompra" type="number" step="0.01" min="0" size="xl" class="w-full" />
            </UFormField>

            <UFormField label="Precio de venta" name="precioVenta" size="xl" class="w-full">
              <UInput v-model.number="state.precioVenta" type="number" step="0.01" min="0" size="xl" class="w-full" />
            </UFormField>
          </div>

          <UFormField label="Estado" name="activo" size="xl" class="w-full">
            <UCheckbox v-model="state.activo" label="Activo" />
          </UFormField>

          <div class="flex justify-end gap-3 pt-4">
            <UButton type="submit" label="Guardar Repuesto" color="primary" :loading="loading" icon="i-lucide-save" />
            <UButton label="Cancelar" color="neutral" variant="outline" @click="goBack" />
          </div>
        </UForm>
      </div>
    </template>
  </UDashboardPanel>
</template>
