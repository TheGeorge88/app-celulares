<script setup lang="ts">
import { ref, h, resolveComponent } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { TableColumn } from '@nuxt/ui'
import { getPaginationRowModel, getFilteredRowModel } from '@tanstack/table-core'
import type { Row } from '@tanstack/table-core'

interface Equipo {
  id: string
  clienteId: string
  cliente?: { razonSocial: string }
  marca: string
  modelo: string
  imei: string
  color: string
  descripcionCompleta: string
  observaciones?: string
}

const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UCheckbox = resolveComponent('UCheckbox')
const UBadge = resolveComponent('UBadge')
const UModal = resolveComponent('UModal')

const props = defineProps<{
  equipos: { data: Equipo[] }
}>()

const toast = useToast()
const table = useTemplateRef('table')

const globalFilter = ref('')
const rowSelection = ref({})
const isDeleteModalOpen = ref(false)
const equipoToDelete = ref<Equipo | null>(null)

const handleCreate = () => router.visit(route('equipos.create'))
const handleEdit = (equipo: Equipo) => router.visit(route('equipos.edit', equipo.id))

const confirmDelete = (equipo: Equipo) => {
  equipoToDelete.value = equipo
  isDeleteModalOpen.value = true
}

const handleDelete = async () => {
  if (!equipoToDelete.value) return
  try {
    const response = await fetch(route('api.equipos.destroy', equipoToDelete.value.id), {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1] || '')
      },
      credentials: 'include'
    })
    if (response.ok) {
      toast.add({ title: 'Equipo eliminado', color: 'success' })
      router.reload()
    } else {
      const error = await response.json()
      toast.add({ title: 'Error', description: error.message, color: 'error' })
    }
  } finally {
    isDeleteModalOpen.value = false
    equipoToDelete.value = null
  }
}

function getRowItems(row: Row<Equipo>) {
  return [
    { type: 'label', label: 'Acciones' },
    { label: 'Editar equipo', icon: 'i-lucide-pencil', onSelect() { handleEdit(row.original) } },
    { type: 'separator' },
    { label: 'Eliminar equipo', icon: 'i-lucide-trash', color: 'error', onSelect() { confirmDelete(row.original) } }
  ]
}

const columns: TableColumn<Equipo>[] = [
  {
    id: 'select',
    header: ({ table }) => h(UCheckbox, {
      'modelValue': table.getIsSomePageRowsSelected() ? 'indeterminate' : table.getIsAllPageRowsSelected(),
      'onUpdate:modelValue': (value: boolean | 'indeterminate') => table.toggleAllPageRowsSelected(!!value)
    }),
    cell: ({ row }) => h(UCheckbox, {
      'modelValue': row.getIsSelected(),
      'onUpdate:modelValue': (value: boolean | 'indeterminate') => row.toggleSelected(!!value)
    })
  },
  {
    accessorKey: 'descripcionCompleta',
    header: 'Equipo',
    cell: ({ row }) => h('div', [
      h('p', { class: 'font-medium' }, row.original.descripcionCompleta),
      h('p', { class: 'text-sm text-muted' }, `IMEI: ${row.original.imei}`)
    ])
  },
  {
    accessorKey: 'cliente',
    header: 'Cliente',
    cell: ({ row }) => row.original.cliente?.razonSocial || '-'
  },
  { accessorKey: 'marca', header: 'Marca' },
  { accessorKey: 'modelo', header: 'Modelo' },
  { accessorKey: 'color', header: 'Color' },
  {
    id: 'actions',
    cell: ({ row }) => h('div', { class: 'text-right' },
      h(UDropdownMenu, { content: { align: 'end' }, items: getRowItems(row) }, () =>
        h(UButton, { icon: 'i-lucide-ellipsis-vertical', color: 'neutral', variant: 'ghost' })
      )
    )
  }
]

const pagination = ref({ pageIndex: 0, pageSize: 10 })
</script>

<template>
  <UDashboardPanel id="equipos">
    <template #header>
      <UDashboardNavbar title="Equipos">
        <template #leading><UDashboardSidebarCollapse /></template>
        <template #right>
          <UButton label="Nuevo Equipo" color="primary" icon="i-lucide-plus" @click="handleCreate" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5 mb-4">
        <UInput v-model="globalFilter" class="w-full sm:w-1/2" size="xl" icon="i-lucide-search" placeholder="Buscar equipo..." />
      </div>

      <UTable
        ref="table"
        v-model:row-selection="rowSelection"
        v-model:pagination="pagination"
        v-model:global-filter="globalFilter"
        :pagination-options="{ getPaginationRowModel: getPaginationRowModel() }"
        :filter-options="{ getFilteredRowModel: getFilteredRowModel() }"
        :data="equipos.data"
        :columns="columns"
      />

      <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto">
        <div class="text-sm text-muted">
          {{ table?.tableApi?.getFilteredSelectedRowModel().rows.length || 0 }} de
          {{ table?.tableApi?.getFilteredRowModel().rows.length || 0 }} fila(s).
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

  <UModal v-model:open="isDeleteModalOpen" title="Confirmar eliminacion">
    <template #footer>
      <div class="flex justify-end gap-3">
        <UButton label="Eliminar" color="error" @click="handleDelete" />
        <UButton label="Cancelar" color="neutral" variant="subtle" @click="isDeleteModalOpen = false" />
      </div>
    </template>
  </UModal>
</template>
