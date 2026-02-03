<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

interface Orden {
  id: string
  codigoSeguimiento: string
  cliente?: { razonSocial: string; numeroDocumento: string; telefono: string; email: string }
  equipo?: { marca: string; modelo: string; imei: string; color: string }
  tecnico?: { nombreCompleto: string; especialidad: string }
  problemaReportado: string
  diagnostico?: string
  solucionAplicada?: string
  estado: string
  estadoDescripcion: string
  costoEstimado?: number
  costoFinal?: number
  autorizado: boolean
  fechaAutorizacion?: string
  fechaEntrega?: string
  observaciones?: string
  createdAt: string
}

const props = defineProps<{
  orden: { data: Orden }
}>()

const toast = useToast()

const getEstadoColor = (estado: string) => {
  const colores: Record<string, string> = {
    'RECIBIDO': 'info', 'EN_DIAGNOSTICO': 'warning', 'PENDIENTE_AUTORIZACION': 'orange',
    'AUTORIZADO': 'teal', 'EN_REPARACION': 'yellow', 'REPARADO': 'success',
    'ENTREGADO': 'neutral', 'CANCELADO': 'error'
  }
  return colores[estado] || 'neutral'
}

const goBack = () => router.visit(route('ordenes-reparacion.index'))
const goEdit = () => router.visit(route('ordenes-reparacion.edit', props.orden.data.id))

const copiarCodigo = () => {
  navigator.clipboard.writeText(props.orden.data.codigoSeguimiento)
  toast.add({ title: 'Codigo copiado al portapapeles' })
}
</script>

<template>
  <UDashboardPanel id="orden-show">
    <template #header>
      <UDashboardNavbar :title="`Orden ${orden.data.codigoSeguimiento}`">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
        <template #right>
          <UButton label="Editar" icon="i-lucide-pencil" @click="goEdit" />
          <UButton label="Copiar Codigo" icon="i-lucide-copy" color="neutral" variant="outline" @click="copiarCodigo" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="max-w-4xl space-y-6">
        <!-- Estado -->
        <UCard>
          <template #header>
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold">Estado de la Orden</h3>
              <UBadge :color="getEstadoColor(orden.data.estado)" size="lg">
                {{ orden.data.estadoDescripcion }}
              </UBadge>
            </div>
          </template>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
              <p class="text-muted">Fecha recepcion</p>
              <p class="font-medium">{{ orden.data.createdAt }}</p>
            </div>
            <div v-if="orden.data.fechaAutorizacion">
              <p class="text-muted">Fecha autorizacion</p>
              <p class="font-medium">{{ orden.data.fechaAutorizacion }}</p>
            </div>
            <div v-if="orden.data.fechaEntrega">
              <p class="text-muted">Fecha entrega</p>
              <p class="font-medium">{{ orden.data.fechaEntrega }}</p>
            </div>
            <div>
              <p class="text-muted">Autorizado</p>
              <UBadge :color="orden.data.autorizado ? 'success' : 'warning'">
                {{ orden.data.autorizado ? 'Si' : 'No' }}
              </UBadge>
            </div>
          </div>
        </UCard>

        <!-- Cliente y Equipo -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <UCard>
            <template #header><h3 class="font-semibold">Cliente</h3></template>
            <div class="space-y-2 text-sm" v-if="orden.data.cliente">
              <p><span class="text-muted">Nombre:</span> {{ orden.data.cliente.razonSocial }}</p>
              <p><span class="text-muted">Documento:</span> {{ orden.data.cliente.numeroDocumento }}</p>
              <p><span class="text-muted">Telefono:</span> {{ orden.data.cliente.telefono }}</p>
              <p><span class="text-muted">Email:</span> {{ orden.data.cliente.email }}</p>
            </div>
          </UCard>

          <UCard>
            <template #header><h3 class="font-semibold">Equipo</h3></template>
            <div class="space-y-2 text-sm" v-if="orden.data.equipo">
              <p><span class="text-muted">Marca:</span> {{ orden.data.equipo.marca }}</p>
              <p><span class="text-muted">Modelo:</span> {{ orden.data.equipo.modelo }}</p>
              <p><span class="text-muted">IMEI:</span> {{ orden.data.equipo.imei }}</p>
              <p><span class="text-muted">Color:</span> {{ orden.data.equipo.color }}</p>
            </div>
          </UCard>
        </div>

        <!-- Detalles de reparacion -->
        <UCard>
          <template #header><h3 class="font-semibold">Detalles de Reparacion</h3></template>
          <div class="space-y-4">
            <div>
              <p class="text-muted text-sm">Problema reportado</p>
              <p>{{ orden.data.problemaReportado }}</p>
            </div>
            <div v-if="orden.data.diagnostico">
              <p class="text-muted text-sm">Diagnostico</p>
              <p>{{ orden.data.diagnostico }}</p>
            </div>
            <div v-if="orden.data.solucionAplicada">
              <p class="text-muted text-sm">Solucion aplicada</p>
              <p>{{ orden.data.solucionAplicada }}</p>
            </div>
            <div v-if="orden.data.tecnico">
              <p class="text-muted text-sm">Tecnico asignado</p>
              <p>{{ orden.data.tecnico.nombreCompleto }} - {{ orden.data.tecnico.especialidad }}</p>
            </div>
          </div>
        </UCard>

        <!-- Costos -->
        <UCard>
          <template #header><h3 class="font-semibold">Costos</h3></template>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-muted text-sm">Costo estimado</p>
              <p class="text-xl font-bold">{{ orden.data.costoEstimado ? `$${orden.data.costoEstimado.toFixed(2)}` : '-' }}</p>
            </div>
            <div>
              <p class="text-muted text-sm">Costo final</p>
              <p class="text-xl font-bold text-primary">{{ orden.data.costoFinal ? `$${orden.data.costoFinal.toFixed(2)}` : '-' }}</p>
            </div>
          </div>
        </UCard>

        <!-- Observaciones -->
        <UCard v-if="orden.data.observaciones">
          <template #header><h3 class="font-semibold">Observaciones</h3></template>
          <p class="whitespace-pre-wrap">{{ orden.data.observaciones }}</p>
        </UCard>
      </div>
    </template>
  </UDashboardPanel>
</template>
