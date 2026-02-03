<script setup lang="ts">
import { ref } from 'vue'
import { route } from 'ziggy-js'

const numeroDocumento = ref('')
const loading = ref(false)
const error = ref('')
const ordenes = ref<any[]>([])
const consultaRealizada = ref(false)

const consultar = async () => {
  if (!numeroDocumento.value.trim()) {
    error.value = 'Ingrese su numero de documento'
    return
  }

  loading.value = true
  error.value = ''
  ordenes.value = []

  try {
    const response = await fetch(route('api.consulta-estado.historial'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ numeroDocumento: numeroDocumento.value.trim() })
    })

    const data = await response.json()
    consultaRealizada.value = true
    ordenes.value = data.ordenes || []

    if (ordenes.value.length === 0) {
      error.value = 'No se encontraron ordenes para este numero de documento'
    }
  } catch (e) {
    error.value = 'Error de conexion. Intente nuevamente.'
  } finally {
    loading.value = false
  }
}

const getEstadoColor = (estado: string) => {
  const colores: Record<string, string> = {
    'RECIBIDO': 'info', 'EN_DIAGNOSTICO': 'warning', 'PENDIENTE_AUTORIZACION': 'orange',
    'AUTORIZADO': 'teal', 'EN_REPARACION': 'yellow', 'REPARADO': 'success',
    'ENTREGADO': 'neutral', 'CANCELADO': 'error'
  }
  return colores[estado] || 'neutral'
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4">
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold">Historial de Reparaciones</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Consulte todas sus ordenes de reparacion</p>
      </div>

      <!-- Formulario -->
      <UCard class="mb-6">
        <div class="flex gap-3">
          <UInput
            v-model="numeroDocumento"
            placeholder="Ingrese su cedula o RUC"
            size="xl"
            class="flex-1"
            @keyup.enter="consultar"
          />
          <UButton label="Buscar" color="primary" size="xl" :loading="loading" @click="consultar" />
        </div>
        <p v-if="error" class="mt-3 text-error text-sm">{{ error }}</p>
      </UCard>

      <!-- Resultados -->
      <div v-if="ordenes.length > 0" class="space-y-4">
        <UCard v-for="orden in ordenes" :key="orden.codigoSeguimiento">
          <div class="flex items-start justify-between">
            <div>
              <p class="font-mono font-semibold text-primary">{{ orden.codigoSeguimiento }}</p>
              <p class="font-medium mt-1">{{ orden.equipo }}</p>
              <p class="text-sm text-muted">Recibido: {{ orden.fechaRecepcion }}</p>
              <p v-if="orden.fechaEntrega" class="text-sm text-muted">Entregado: {{ orden.fechaEntrega }}</p>
              <p v-if="orden.costoFinal" class="text-lg font-bold mt-2">${{ orden.costoFinal.toFixed(2) }}</p>
            </div>
            <div class="text-right">
              <UBadge :color="getEstadoColor(orden.estado)">
                {{ orden.estadoDescripcion }}
              </UBadge>
              <div class="mt-3">
                <UButton
                  label="Ver detalles"
                  size="sm"
                  color="neutral"
                  variant="outline"
                  :to="`/consulta/resultado/${orden.codigoSeguimiento}`"
                />
              </div>
            </div>
          </div>
        </UCard>
      </div>

      <!-- Sin resultados -->
      <UCard v-else-if="consultaRealizada && ordenes.length === 0" class="text-center py-8">
        <UIcon name="i-lucide-inbox" class="w-12 h-12 text-muted mx-auto mb-4" />
        <p class="text-muted">No se encontraron ordenes de reparacion</p>
      </UCard>

      <!-- Volver -->
      <div class="mt-6 text-center">
        <UButton label="Volver a consulta" color="neutral" variant="link" to="/consulta" />
      </div>
    </div>
  </div>
</template>
