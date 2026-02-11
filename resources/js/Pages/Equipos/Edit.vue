<script setup lang="ts">
import { reactive, ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import FormField from '../../components/FormField.vue'

interface Cliente { id: string; razonSocial: string; numeroDocumento: string }
interface Equipo {
  id: string; clienteId: string; marca: string; modelo: string
  imei: string; color: string; observaciones?: string
}

const props = defineProps<{
  equipo: Equipo
  clientes: { data: Cliente[] }
}>()

const state = reactive({
  clienteId: props.equipo.clienteId,
  marca: props.equipo.marca,
  modelo: props.equipo.modelo,
  imei: props.equipo.imei,
  color: props.equipo.color,
  observaciones: props.equipo.observaciones || ''
})

const page = usePage()
const backendErrors = computed(() => page.props.errors || {})

const errors = computed(() => {
  const result: Record<string, string> = {}
  Object.keys(backendErrors.value).forEach(key => {
    const error = backendErrors.value[key]
    result[key] = Array.isArray(error) ? error[0] : error
  })
  return result
})

const isLoading = ref(false)

const clienteOptions = computed(() =>
  props.clientes.data.map(c => ({ label: `${c.razonSocial} - ${c.numeroDocumento}`, value: c.id }))
)

const handleSubmit = () => {
  isLoading.value = true

  router.put(route('equipos.update', props.equipo.id), state, {
    onFinish: () => { isLoading.value = false },
    onError: () => { isLoading.value = false }
  })
}

const goBack = () => router.visit(route('equipos.index'))
</script>

<template>
  <UDashboardPanel id="equipos-edit">
    <template #header>
      <UDashboardNavbar title="Editar Equipo">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold">Editar Equipo</h2>
          <p class="text-sm text-muted mt-1">Modifique los datos del equipo</p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6 max-w-2xl">
          <FormField label="Cliente" name="clienteId" required :error="errors.cliente_id">
            <USelectMenu v-model="state.clienteId" :items="clienteOptions" placeholder="Seleccione un cliente" size="xl" class="w-full" />
          </FormField>

          <div class="grid grid-cols-2 gap-8">
            <FormField label="Marca" name="marca" required :error="errors.marca">
              <UInput v-model="state.marca" icon="i-lucide-smartphone" size="xl" class="w-full" />
            </FormField>

            <FormField label="Modelo" name="modelo" required :error="errors.modelo">
              <UInput v-model="state.modelo" size="xl" class="w-full" />
            </FormField>
          </div>

          <div class="grid grid-cols-2 gap-8">
            <FormField label="IMEI" name="imei" required :error="errors.imei">
              <UInput v-model="state.imei" icon="i-lucide-barcode" size="xl" class="w-full" />
            </FormField>

            <FormField label="Color" name="color" required :error="errors.color">
              <UInput v-model="state.color" icon="i-lucide-palette" size="xl" class="w-full" />
            </FormField>
          </div>

          <FormField label="Observaciones" name="observaciones" :error="errors.observaciones">
            <UTextarea v-model="state.observaciones" size="xl" class="w-full" />
          </FormField>

          <div class="flex justify-end gap-3 pt-4">
            <UButton type="button" color="neutral" variant="outline" label="Cancelar" @click="goBack" :disabled="isLoading" />
            <UButton type="submit" color="primary" label="Actualizar Equipo" icon="i-lucide-save" :loading="isLoading" />
          </div>
        </form>
      </div>
    </template>
  </UDashboardPanel>
</template>
