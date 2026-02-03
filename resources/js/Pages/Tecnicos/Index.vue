<script setup lang="ts">
import { ref, computed, h, resolveComponent } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { TableColumn } from '@nuxt/ui'
import { upperFirst } from 'scule'
import { getPaginationRowModel, getFilteredRowModel } from '@tanstack/table-core'
import type { Row } from '@tanstack/table-core'

interface Tecnico {
  id: string
  cedula: string
  nombre: string
  apellido: string
  nombreCompleto: string
  telefono: string
  email: string
  especialidad: string
  activo: boolean
}

const UAvatar = resolveComponent('UAvatar')
const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UCheckbox = resolveComponent('UCheckbox')
const UBadge = resolveComponent('UBadge')
const UModal = resolveComponent('UModal')

const props = defineProps<{
  tecnicos: { data: Tecnico[] }
}>()

const toast = useToast()
const table = useTemplateRef('table')

const globalFilter = ref('')
const columnVisibility = ref()
const rowSelection = ref({})

const isDeleteModalOpen = ref(false)
const tecnicoToDelete = ref<Tecnico | null>(null)

const handleCreate = () => {
  router.visit(route('tecnicos.create'))
}

const handleEdit = (tecnico: Tecnico) => {
  router.visit(route('tecnicos.edit', tecnico.id))
}

const confirmDelete = (tecnico: Tecnico) => {
  tecnicoToDelete.value = tecnico
  isDeleteModalOpen.value = true
}

const handleDelete = () => {
  if (!tecnicoToDelete.value) return

  router.delete(route('api.tecnicos.destroy', tecnicoToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      isDeleteModalOpen.value = false
      tecnicoToDelete.value = null
      toast.add({ title: 'Tecnico eliminado correctamente' })
    },
    onError: (errors) => {
      toast.add({ title: 'Error al eliminar', description: errors.message, color: 'error' })
    }
  })
}

function getRowItems(row: Row<Tecnico>) {
  return [
    { type: 'label', label: 'Acciones' },
    {
      label: 'Editar tecnico',
      icon: 'i-lucide-pencil',
      onSelect() { handleEdit(row.original) }
    },
    { type: 'separator' },
    {
      label: 'Eliminar tecnico',
      icon: 'i-lucide-trash',
      color: 'error',
      onSelect() { confirmDelete(row.original) }
    }
  ]
}

const columns: TableColumn<Tecnico>[] = [
  {
    id: 'select',
    header: ({ table }) => h(UCheckbox, {
      'modelValue': table.getIsSomePageRowsSelected() ? 'indeterminate' : table.getIsAllPageRowsSelected(),
      'onUpdate:modelValue': (value: boolean | 'indeterminate') => table.toggleAllPageRowsSelected(!!value),
      'ariaLabel': 'Seleccionar todos'
    }),
    cell: ({ row }) => h(UCheckbox, {
      'modelValue': row.getIsSelected(),
      'onUpdate:modelValue': (value: boolean | 'indeterminate') => row.toggleSelected(!!value),
      'ariaLabel': 'Seleccionar fila'
    })
  },
  {
    accessorKey: 'nombreCompleto',
    header: 'Nombre',
    cell: ({ row }) => h('div', { class: 'flex items-center gap-3' }, [
      h(UAvatar, {
        src: `https://ui-avatars.com/api/?name=${encodeURIComponent(row.original.nombreCompleto)}&background=random`,
        alt: row.original.nombreCompleto,
        size: 'lg'
      }),
      h('div', undefined, [
        h('p', { class: 'font-medium text-highlighted' }, row.original.nombreCompleto),
        h('p', { class: 'text-sm text-muted' }, row.original.especialidad)
      ])
    ])
  },
  { accessorKey: 'cedula', header: 'Cedula' },
  { accessorKey: 'email', header: 'Email' },
  { accessorKey: 'telefono', header: 'Telefono' },
  {
    accessorKey: 'activo',
    header: 'Estado',
    cell: ({ row }) => h(UBadge, {
      color: row.original.activo ? 'success' : 'error',
      variant: 'subtle'
    }, () => row.original.activo ? 'Activo' : 'Inactivo')
  },
  {
    id: 'actions',
    cell: ({ row }) => h('div', { class: 'text-right' },
      h(UDropdownMenu, { content: { align: 'end' }, items: getRowItems(row) }, () =>
        h(UButton, { icon: 'i-lucide-ellipsis-vertical', color: 'neutral', variant: 'ghost', class: 'ml-auto' })
      )
    )
  }
]

const pagination = ref({ pageIndex: 0, pageSize: 10 })
</script>

<template>
  <UDashboardPanel id="tecnicos">
    <template #header>
      <UDashboardNavbar title="Tecnicos">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
        <template #right>
          <UButton label="Nuevo Tecnico" color="primary" icon="i-lucide-plus" @click="handleCreate" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5 mb-4">
        <UInput v-model="globalFilter" class="w-full sm:w-1/2" size="xl" icon="i-lucide-search" placeholder="Buscar tecnico..." />
      </div>

      <UTable
        ref="table"
        v-model:column-visibility="columnVisibility"
        v-model:row-selection="rowSelection"
        v-model:pagination="pagination"
        v-model:global-filter="globalFilter"
        :pagination-options="{ getPaginationRowModel: getPaginationRowModel() }"
        :filter-options="{ getFilteredRowModel: getFilteredRowModel() }"
        class="shrink-0"
        :data="tecnicos.data"
        :columns="columns"
      />

      <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto">
        <div class="text-sm text-muted">
          {{ table?.tableApi?.getFilteredSelectedRowModel().rows.length || 0 }} de
          {{ table?.tableApi?.getFilteredRowModel().rows.length || 0 }} fila(s) seleccionada(s).
        </div>
        <UPagination
          :default-page="(table?.tableApi?.getState().pagination.pageIndex || 0) + 1"
          :items-per-page="table?.tableApi?.getState().pagination.pageSize"
          :total="table?.tableApi?.getFilteredRowModel().rows.length"
          @update:page="(p: number) => table?.tableApi?.setPageIndex(p - 1)"
        />
      </div>
    </template>
  </UDashboardPanel>

  <UModal v-model:open="isDeleteModalOpen" title="Confirmar eliminacion" :description="`¿Esta seguro de que desea eliminar al tecnico ${tecnicoToDelete?.nombreCompleto}?`">
    <template #footer>
      <div class="flex justify-end gap-3">
        <UButton label="Eliminar" color="error" @click="handleDelete" />
        <UButton label="Cancelar" color="neutral" variant="subtle" @click="isDeleteModalOpen = false" />
      </div>
    </template>
  </UModal>
</template>
