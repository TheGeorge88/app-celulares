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

  router.put(route('ordenes-reparacion.update', props.orden.data.id), event.data, {
    onSuccess: () => {
      toast.add({ title: 'Orden actualizada', color: 'success' })
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0]
      toast.add({ title: 'Error', description: firstError as string, color: 'error' })
    },
    onFinish: () => { loading.value = false }
  })
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
      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold">Editar Orden {{ orden.data.codigoSeguimiento }}</h2>
          <p class="text-sm text-muted mt-1">Modifique los datos de la orden</p>
        </div>

        <UForm :schema="schema" :state="state" class="space-y-6 max-w-2xl" @submit="onSubmit">
          <div class="grid grid-cols-2 gap-8">
            <UFormField label="Estado" name="estado" size="xl" class="w-full">
              <USelectMenu v-model="state.estado" :items="estados" size="xl" class="w-full" />
            </UFormField>

            <UFormField label="Tecnico asignado" name="tecnicoId" size="xl" class="w-full">
              <USelectMenu v-model="state.tecnicoId" :items="tecnicoOptions" size="xl" class="w-full" />
            </UFormField>
          </div>

          <UFormField label="Problema reportado" name="problemaReportado" size="xl" class="w-full">
            <UTextarea v-model="state.problemaReportado" rows="3" size="xl" class="w-full" />
          </UFormField>

          <UFormField label="Diagnostico" name="diagnostico" size="xl" class="w-full">
            <UTextarea v-model="state.diagnostico" rows="3" placeholder="Resultado del diagnostico tecnico..." size="xl" class="w-full" />
          </UFormField>

          <UFormField label="Solucion aplicada" name="solucionAplicada" size="xl" class="w-full">
            <UTextarea v-model="state.solucionAplicada" rows="3" placeholder="Descripcion de la reparacion realizada..." size="xl" class="w-full" />
          </UFormField>

          <div class="grid grid-cols-2 gap-8">
            <UFormField label="Costo estimado" name="costoEstimado" size="xl" class="w-full">
              <UInput v-model.number="state.costoEstimado" type="number" step="0.01" size="xl" class="w-full" />
            </UFormField>

            <UFormField label="Costo final" name="costoFinal" size="xl" class="w-full">
              <UInput v-model.number="state.costoFinal" type="number" step="0.01" size="xl" class="w-full" />
            </UFormField>
          </div>

          <UFormField label="Observaciones" name="observaciones" size="xl" class="w-full">
            <UTextarea v-model="state.observaciones" rows="2" size="xl" class="w-full" />
          </UFormField>

          <div class="flex justify-end gap-3 pt-4">
            <UButton type="submit" label="Guardar cambios" color="primary" :loading="loading" icon="i-lucide-save" />
            <UButton label="Cancelar" color="neutral" variant="outline" @click="goBack" />
          </div>
        </UForm>
      </div>
    </template>
  </UDashboardPanel>
</template>
