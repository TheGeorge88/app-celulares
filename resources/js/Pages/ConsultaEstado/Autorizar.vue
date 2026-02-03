<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { route } from 'ziggy-js'

const props = defineProps<{
  codigoSeguimiento: string
}>()

const numeroDocumento = ref('')
const observaciones = ref('')
const loading = ref(false)
const loadingConsulta = ref(true)
const error = ref('')
const success = ref(false)
const successMessage = ref('')
const orden = ref<any>(null)

onMounted(async () => {
  try {
    const response = await fetch(route('api.consulta-estado.consultar'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ codigoSeguimiento: props.codigoSeguimiento })
    })
    const data = await response.json()
    if (response.ok && data.encontrado) {
      orden.value = data.orden
    } else {
      error.value = 'No se encontro la orden'
    }
  } catch (e) {
    error.value = 'Error de conexion'
  } finally {
    loadingConsulta.value = false
  }
})

const autorizar = async (aceptar: boolean) => {
  if (!numeroDocumento.value.trim()) {
    error.value = 'Ingrese su numero de documento para verificar su identidad'
    return
  }

  loading.value = true
  error.value = ''

  try {
    const response = await fetch(route('api.consulta-estado.autorizar'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({
        codigoSeguimiento: props.codigoSeguimiento,
        numeroDocumento: numeroDocumento.value.trim(),
        autorizar: aceptar,
        observacionesCliente: observaciones.value
      })
    })

    const data = await response.json()

    if (response.ok && data.success) {
      success.value = true
      successMessage.value = data.message
    } else {
      error.value = data.message || 'Error al procesar la autorizacion'
    }
  } catch (e) {
    error.value = 'Error de conexion. Intente nuevamente.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4">
    <div class="max-w-xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold">Autorizar Reparacion</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Orden: <span class="font-mono font-semibold">{{ codigoSeguimiento }}</span></p>
      </div>

      <!-- Loading -->
      <div v-if="loadingConsulta" class="text-center py-12">
        <UIcon name="i-lucide-loader-2" class="w-8 h-8 animate-spin text-primary" />
        <p class="mt-2 text-muted">Cargando informacion...</p>
      </div>

      <!-- Success -->
      <UCard v-else-if="success" class="text-center">
        <div class="py-8">
          <div class="w-16 h-16 mx-auto rounded-full bg-success/20 flex items-center justify-center mb-4">
            <UIcon name="i-lucide-check" class="w-8 h-8 text-success" />
          </div>
          <h2 class="text-xl font-semibold mb-2">Procesado correctamente</h2>
          <p class="text-gray-600 dark:text-gray-400 mb-6">{{ successMessage }}</p>
          <UButton label="Volver a consulta" to="/consulta" color="primary" />
        </div>
      </UCard>

      <!-- Error inicial -->
      <UCard v-else-if="error && !orden" class="text-center">
        <div class="py-8">
          <UIcon name="i-lucide-alert-circle" class="w-12 h-12 text-error mx-auto mb-4" />
          <p class="text-error mb-4">{{ error }}</p>
          <UButton label="Volver" to="/consulta" color="neutral" />
        </div>
      </UCard>

      <!-- Formulario de autorizacion -->
      <template v-else-if="orden">
        <UCard class="mb-6">
          <template #header>
            <h3 class="font-semibold">Resumen de la reparacion</h3>
          </template>

          <div class="space-y-4">
            <div>
              <p class="text-sm text-muted">Equipo</p>
              <p class="font-medium">{{ orden.equipo.marca }} {{ orden.equipo.modelo }}</p>
            </div>

            <div>
              <p class="text-sm text-muted">Diagnostico</p>
              <p>{{ orden.diagnostico || 'Sin diagnostico' }}</p>
            </div>

            <div class="bg-primary/10 rounded-lg p-4">
              <p class="text-sm text-muted">Costo estimado de reparacion</p>
              <p class="text-3xl font-bold text-primary">${{ orden.costoEstimado?.toFixed(2) || '0.00' }}</p>
            </div>
          </div>
        </UCard>

        <UCard>
          <template #header>
            <h3 class="font-semibold">Verificacion de identidad</h3>
          </template>

          <div class="space-y-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Para autorizar o rechazar la reparacion, ingrese el numero de documento registrado al momento de dejar su equipo.
            </p>

            <UFormField label="Numero de documento">
              <UInput v-model="numeroDocumento" placeholder="Cedula o RUC" />
            </UFormField>

            <UFormField label="Observaciones (opcional)">
              <UTextarea v-model="observaciones" placeholder="Algun comentario adicional..." rows="2" />
            </UFormField>

            <p v-if="error" class="text-error text-sm">{{ error }}</p>

            <div class="flex gap-3 pt-4">
              <UButton
                label="Autorizar reparacion"
                color="success"
                class="flex-1"
                :loading="loading"
                @click="autorizar(true)"
              />
              <UButton
                label="Rechazar"
                color="error"
                variant="outline"
                class="flex-1"
                :loading="loading"
                @click="autorizar(false)"
              />
            </div>
          </div>
        </UCard>

        <div class="mt-6 text-center">
          <UButton label="Volver a consulta" color="neutral" variant="link" to="/consulta" />
        </div>
      </template>
    </div>
  </div>
</template>
