<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const codigoSeguimiento = ref('')
const loading = ref(false)
const error = ref('')
const resultado = ref<any>(null)

const consultar = async () => {
  if (!codigoSeguimiento.value.trim()) {
    error.value = 'Ingrese un codigo de seguimiento'
    return
  }

  loading.value = true
  error.value = ''
  resultado.value = null

  try {
    const response = await fetch(route('api.consulta-estado.consultar'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ codigoSeguimiento: codigoSeguimiento.value.trim() })
    })

    const data = await response.json()

    if (response.ok && data.encontrado) {
      resultado.value = data
    } else {
      error.value = data.message || 'No se encontro la orden'
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
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 mb-4">
          <UIcon name="i-lucide-smartphone" class="w-8 h-8 text-primary" />
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Consulta de Estado</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Ingrese su codigo de seguimiento para ver el estado de su reparacion</p>
      </div>

      <!-- Formulario de consulta -->
      <UCard class="mb-6">
        <div class="flex gap-3">
          <UInput
            v-model="codigoSeguimiento"
            placeholder="REP-20260202-XXXXXX"
            size="xl"
            class="flex-1"
            @keyup.enter="consultar"
          />
          <UButton
            label="Consultar"
            color="primary"
            size="xl"
            :loading="loading"
            @click="consultar"
          />
        </div>
        <p v-if="error" class="mt-3 text-error text-sm">{{ error }}</p>
      </UCard>

      <!-- Resultado -->
      <template v-if="resultado">
        <UCard class="mb-6">
          <template #header>
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-muted">Codigo de seguimiento</p>
                <p class="text-xl font-mono font-bold">{{ resultado.orden.codigoSeguimiento }}</p>
              </div>
              <UBadge :color="getEstadoColor(resultado.orden.estado)" size="lg">
                {{ resultado.orden.estadoDescripcion }}
              </UBadge>
            </div>
          </template>

          <div class="space-y-6">
            <!-- Equipo -->
            <div>
              <h3 class="font-semibold mb-2">Equipo</h3>
              <p>{{ resultado.orden.equipo.marca }} {{ resultado.orden.equipo.modelo }} - {{ resultado.orden.equipo.color }}</p>
            </div>

            <!-- Problema -->
            <div>
              <h3 class="font-semibold mb-2">Problema reportado</h3>
              <p class="text-gray-600 dark:text-gray-400">{{ resultado.orden.problemaReportado }}</p>
            </div>

            <!-- Diagnostico -->
            <div v-if="resultado.orden.diagnostico">
              <h3 class="font-semibold mb-2">Diagnostico</h3>
              <p class="text-gray-600 dark:text-gray-400">{{ resultado.orden.diagnostico }}</p>
            </div>

            <!-- Costos -->
            <div v-if="resultado.orden.costoEstimado" class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-muted">Costo estimado</p>
                <p class="text-2xl font-bold">${{ resultado.orden.costoEstimado.toFixed(2) }}</p>
              </div>
              <div v-if="resultado.orden.costoFinal">
                <p class="text-sm text-muted">Costo final</p>
                <p class="text-2xl font-bold text-primary">${{ resultado.orden.costoFinal.toFixed(2) }}</p>
              </div>
            </div>

            <!-- Tecnico -->
            <div v-if="resultado.orden.tecnicoAsignado">
              <h3 class="font-semibold mb-2">Tecnico asignado</h3>
              <p>{{ resultado.orden.tecnicoAsignado }}</p>
            </div>

            <!-- Fechas -->
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <p class="text-muted">Fecha de recepcion</p>
                <p class="font-medium">{{ resultado.orden.fechaRecepcion }}</p>
              </div>
              <div v-if="resultado.orden.fechaEntrega">
                <p class="text-muted">Fecha de entrega</p>
                <p class="font-medium">{{ resultado.orden.fechaEntrega }}</p>
              </div>
            </div>

            <!-- Requiere autorizacion -->
            <div v-if="resultado.orden.requiereAutorizacion" class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
              <div class="flex items-start gap-3">
                <UIcon name="i-lucide-alert-circle" class="w-6 h-6 text-orange-500 flex-shrink-0 mt-0.5" />
                <div>
                  <h4 class="font-semibold text-orange-800 dark:text-orange-200">Autorizacion requerida</h4>
                  <p class="text-sm text-orange-700 dark:text-orange-300 mt-1">
                    Se requiere su autorizacion para proceder con la reparacion. El costo estimado es de ${{ resultado.orden.costoEstimado?.toFixed(2) }}.
                  </p>
                  <UButton
                    label="Autorizar reparacion"
                    color="warning"
                    class="mt-3"
                    :to="`/consulta/autorizar/${resultado.orden.codigoSeguimiento}`"
                  />
                </div>
              </div>
            </div>
          </div>
        </UCard>

        <!-- Timeline -->
        <UCard>
          <template #header>
            <h3 class="font-semibold">Seguimiento</h3>
          </template>
          <div class="space-y-4">
            <div
              v-for="(step, index) in resultado.timeline"
              :key="step.estado"
              class="flex gap-4"
            >
              <div class="flex flex-col items-center">
                <div
                  class="w-8 h-8 rounded-full flex items-center justify-center"
                  :class="step.completado ? 'bg-primary text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400'"
                >
                  <UIcon v-if="step.completado" name="i-lucide-check" class="w-4 h-4" />
                  <span v-else class="text-xs">{{ index + 1 }}</span>
                </div>
                <div v-if="index < resultado.timeline.length - 1" class="w-0.5 h-full min-h-[2rem] bg-gray-200 dark:bg-gray-700" :class="{ 'bg-primary': step.completado }"></div>
              </div>
              <div class="pb-4">
                <p class="font-medium" :class="{ 'text-primary': step.actual }">{{ step.descripcion }}</p>
                <p v-if="step.fecha" class="text-sm text-muted">{{ step.fecha }}</p>
              </div>
            </div>
          </div>
        </UCard>
      </template>

      <!-- Link a historial -->
      <div class="mt-8 text-center">
        <UButton
          label="Ver historial completo"
          color="neutral"
          variant="link"
          to="/consulta/historial"
        />
      </div>
    </div>
  </div>
</template>
