<script setup lang="ts">
import { reactive, ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

interface Usuario {
  id: string
  name: string
  email: string
}

interface Tecnico {
  id: string
  userId: string
  usuario: string
  email: string
  especialidad: string
  certificacion: string | null
  fechaContratacion: string | null
  activo: boolean
}

const props = defineProps<{
  tecnico: { data: Tecnico }
  usuarios: Usuario[]
}>()

const state = reactive({
  userId: props.tecnico.data.userId,
  especialidad: props.tecnico.data.especialidad,
  certificacion: props.tecnico.data.certificacion || '',
  fechaContratacion: props.tecnico.data.fechaContratacion || '',
  activo: props.tecnico.data.activo,
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

const usuarioOptions = computed(() =>
  props.usuarios.map(u => ({ label: `${u.name} - ${u.email}`, value: u.id }))
)

const handleSubmit = () => {
  isLoading.value = true

  router.put(route('tecnicos.update', props.tecnico.data.id), state, {
    onFinish: () => {
      isLoading.value = false
    },
    onError: (errors) => {
      console.error('Errores de validacion:', errors)
      isLoading.value = false
    }
  })
}

const handleCancel = () => {
  router.visit(route('tecnicos.index'))
}
</script>

<template>
  <UDashboardPanel>
    <template #header>
      <UDashboardNavbar title="Editar Tecnico">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="handleCancel" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold">Editar Tecnico</h2>
          <p class="text-sm text-muted mt-1">Modifique los datos del tecnico</p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6 max-w-2xl">
          <!-- Usuario -->
          <UFormField label="Usuario" name="userId" required :error="errors.user_id" size="xl" class="w-full">
            <USelectMenu
              v-model="state.userId"
              :items="usuarioOptions"
              placeholder="Seleccione un usuario..."
              size="xl"
              class="w-full"
            />
          </UFormField>

          <!-- Fila 1: Especialidad y Certificacion -->
          <div class="grid grid-cols-2 gap-8">
            <UFormField label="Especialidad" name="especialidad" required :error="errors.especialidad" size="xl" class="w-full">
              <UInput
                v-model="state.especialidad"
                placeholder="Ej: Reparacion de pantallas, microsoldadura..."
                icon="i-lucide-wrench"
                size="xl"
                class="w-full"
              />
            </UFormField>

            <UFormField label="Certificacion" name="certificacion" :error="errors.certificacion" size="xl" class="w-full">
              <UInput
                v-model="state.certificacion"
                placeholder="Ej: CERT-001234"
                icon="i-lucide-badge-check"
                size="xl"
                class="w-full"
              />
            </UFormField>
          </div>

          <!-- Fila 2: Fecha de Contratacion y Estado -->
          <div class="grid grid-cols-2 gap-8">
            <UFormField label="Fecha de Contratacion" name="fechaContratacion" :error="errors.fecha_contratacion" size="xl" class="w-full">
              <UInput
                v-model="state.fechaContratacion"
                type="date"
                size="xl"
                class="w-full"
              />
            </UFormField>

            <UFormField label="Estado" name="activo" :error="errors.activo" size="xl" class="w-full">
              <UCheckbox v-model="state.activo" label="Activo" />
            </UFormField>
          </div>

          <!-- Botones -->
          <div class="flex justify-end gap-3 pt-4">
            <UButton
              type="button"
              color="neutral"
              variant="outline"
              label="Cancelar"
              @click="handleCancel"
              :disabled="isLoading"
            />
            <UButton
              type="submit"
              color="primary"
              label="Actualizar Tecnico"
              icon="i-lucide-save"
              :loading="isLoading"
            />
          </div>
        </form>
      </div>
    </template>
  </UDashboardPanel>
</template>
