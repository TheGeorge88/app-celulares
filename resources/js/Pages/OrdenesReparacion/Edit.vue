<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { z } from 'zod'
import type { FormSubmitEvent } from '@nuxt/ui'

interface Tecnico { id: string; nombreCompleto: string; especialidad: string }
interface Orden {
  id: string; codigoSeguimiento: string; tecnicoId?: string; problemaReportado: string
  diagnostico?: string; solucionAplicada?: string; estado: string
  costoEstimado?: number; costoFinal?: number; observaciones?: string
}

const props = defineProps<{
  orden: { data: Orden }
  tecnicos: { data: Tecnico[] }
}>()

const toast = useToast()

const estados = [
  { label: 'Recibido', value: 'RECIBIDO' },
  { label: 'En diagnostico', value: 'EN_DIAGNOSTICO' },
  { label: 'Pendiente autorizacion', value: 'PENDIENTE_AUTORIZACION' },
  { label: 'Autorizado', value: 'AUTORIZADO' },
  { label: 'En reparacion', value: 'EN_REPARACION' },
  { label: 'Reparado', value: 'REPARADO' },
  { label: 'Entregado', value: 'ENTREGADO' },
  { label: 'Cancelado', value: 'CANCELADO' }
]

const schema = z.object({
  tecnicoId: z.string().optional(),
  problemaReportado: z.string().min(1),
  diagnostico: z.string().optional(),
  solucionAplicada: z.string().optional(),
  estado: z.string(),
  costoEstimado: z.number().optional(),
  costoFinal: z.number().optional(),
  observaciones: z.string().optional()
})

type Schema = z.output<typeof schema>

const state = ref<Partial<Schema>>({
  tecnicoId: props.orden.data.tecnicoId || '',
  problemaReportado: props.orden.data.problemaReportado,
  diagnostico: props.orden.data.diagnostico || '',
  solucionAplicada: props.orden.data.solucionAplicada || '',
  estado: props.orden.data.estado,
  costoEstimado: props.orden.data.costoEstimado,
  costoFinal: props.orden.data.costoFinal,
  observaciones: props.orden.data.observaciones || ''
})

const loading = ref(false)

const tecnicoOptions = computed(() =>
  [{ label: 'Sin asignar', value: '' }, ...props.tecnicos.data.map(t => ({ label: `${t.nombreCompleto} - ${t.especialidad}`, value: t.id }))]
)

async function onSubmit(event: FormSubmitEvent<Schema>) {
  loading.value = true
  try {
    const response = await fetch(route('api.ordenes-reparacion.update', props.orden.data.id), {
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
      toast.add({ title: 'Orden actualizada', color: 'success' })
      router.visit(route('ordenes-reparacion.show', props.orden.data.id))
    } else {
      const error = await response.json()
      toast.add({ title: 'Error', description: error.message, color: 'error' })
    }
  } finally {
    loading.value = false
  }
}

const goBack = () => router.visit(route('ordenes-reparacion.show', props.orden.data.id))
</script>

<template>
  <UDashboardPanel id="orden-edit">
    <template #header>
      <UDashboardNavbar :title="`Editar Orden ${orden.data.codigoSeguimiento}`">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <UForm :schema="schema" :state="state" class="space-y-6 max-w-2xl" @submit="onSubmit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <UFormField label="Estado" name="estado">
            <USelectMenu v-model="state.estado" :items="estados" />
          </UFormField>

          <UFormField label="Tecnico asignado" name="tecnicoId">
            <USelectMenu v-model="state.tecnicoId" :items="tecnicoOptions" />
          </UFormField>
        </div>

        <UFormField label="Problema reportado" name="problemaReportado">
          <UTextarea v-model="state.problemaReportado" rows="3" />
        </UFormField>

        <UFormField label="Diagnostico" name="diagnostico">
          <UTextarea v-model="state.diagnostico" rows="3" placeholder="Resultado del diagnostico tecnico..." />
        </UFormField>

        <UFormField label="Solucion aplicada" name="solucionAplicada">
          <UTextarea v-model="state.solucionAplicada" rows="3" placeholder="Descripcion de la reparacion realizada..." />
        </UFormField>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <UFormField label="Costo estimado" name="costoEstimado">
            <UInput v-model.number="state.costoEstimado" type="number" step="0.01" />
          </UFormField>

          <UFormField label="Costo final" name="costoFinal">
            <UInput v-model.number="state.costoFinal" type="number" step="0.01" />
          </UFormField>
        </div>

        <UFormField label="Observaciones" name="observaciones">
          <UTextarea v-model="state.observaciones" rows="2" />
        </UFormField>

        <div class="flex gap-3">
          <UButton type="submit" label="Guardar cambios" color="primary" :loading="loading" />
          <UButton label="Cancelar" color="neutral" variant="subtle" @click="goBack" />
        </div>
      </UForm>
    </template>
  </UDashboardPanel>
</template>
