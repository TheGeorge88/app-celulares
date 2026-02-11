<script setup lang="ts">
import { ref, h, resolveComponent } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { TableColumn } from '@nuxt/ui'
import { getPaginationRowModel, getFilteredRowModel } from '@tanstack/table-core'
import type { Row } from '@tanstack/table-core'

interface Repuesto {
  id: string
  codigo: string
  nombre: string
  descripcion?: string
  marca: string
  modelo: string
  stock: number
  stockMinimo: number
  stockBajo: boolean
  precioCompra: number
  precioVenta: number
  activo: boolean
}

const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UBadge = resolveComponent('UBadge')
const UModal = resolveComponent('UModal')

const props = defineProps<{
  repuestos: { data: Repuesto[] }
}>()

const toast = useToast()
const table = useTemplateRef('table')
const globalFilter = ref('')
const rowSelection = ref({})
const isDeleteModalOpen = ref(false)
const repuestoToDelete = ref<Repuesto | null>(null)

const handleCreate = () => router.visit(route('repuestos.create'))
const handleEdit = (repuesto: Repuesto) => router.visit(route('repuestos.edit', repuesto.id))
const goBack = () => router.visit(route('repuestos.index'))

const confirmDelete = (repuesto: Repuesto) => {
  repuestoToDelete.value = repuesto
  isDeleteModalOpen.value = true
}

const handleDelete = async () => {
  if (!repuestoToDelete.value) return
  try {
    const response = await fetch(route('api.repuestos.destroy', repuestoToDelete.value.id), {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1] || '')
      },
      credentials: 'include'
    })
    if (response.ok) {
      toast.add({ title: 'Repuesto eliminado', color: 'success' })
      router.reload()
    } else {
      const error = await response.json()
      toast.add({ title: 'Error', description: error.message, color: 'error' })
    }
  } finally {
    isDeleteModalOpen.value = false
    repuestoToDelete.value = null
  }
}

function getRowItems(row: Row<Repuesto>) {
  return [
    { type: 'label', label: 'Acciones' },
    { label: 'Editar repuesto', icon: 'i-lucide-pencil', onSelect() { handleEdit(row.original) } },
    { type: 'separator' },
    { label: 'Eliminar repuesto', icon: 'i-lucide-trash', color: 'error', onSelect() { confirmDelete(row.original) } }
  ]
}

const columns: TableColumn<Repuesto>[] = [
  {
    accessorKey: 'codigo',
    header: 'Codigo',
    cell: ({ row }) => h('span', { class: 'font-mono' }, row.original.codigo)
  },
  {
    accessorKey: 'nombre',
    header: 'Nombre',
    cell: ({ row }) => h('div', [
      h('p', { class: 'font-medium' }, row.original.nombre),
      h('p', { class: 'text-xs text-muted' }, `${row.original.marca} - ${row.original.modelo}`)
    ])
  },
  {
    accessorKey: 'stock',
    header: 'Stock Actual',
    cell: ({ row }) => h('div', { class: 'flex items-center gap-2' }, [
      h('span', { class: 'text-error font-bold' }, row.original.stock),
      h(UBadge, { color: 'error', size: 'xs' }, () => 'Bajo')
    ])
  },
  {
    accessorKey: 'stockMinimo',
    header: 'Stock Minimo',
    cell: ({ row }) => h('span', { class: 'font-semibold' }, row.original.stockMinimo)
  },
  {
    accessorKey: 'precioCompra',
    header: 'P. Compra',
    cell: ({ row }) => `$${row.original.precioCompra.toFixed(2)}`
  },
  {
    accessorKey: 'precioVenta',
    header: 'P. Venta',
    cell: ({ row }) => h('span', { class: 'font-semibold text-primary' }, `$${row.original.precioVenta.toFixed(2)}`)
  },
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
        h(UButton, { icon: 'i-lucide-ellipsis-vertical', color: 'neutral', variant: 'ghost' })
      )
    )
  }
]

const pagination = ref({ pageIndex: 0, pageSize: 10 })
</script>

<template>
  <UDashboardPanel id="repuestos-stock-bajo">
    <template #header>
      <UDashboardNavbar title="Repuestos con Stock Bajo">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" @click="goBack" />
        </template>
        <template #right>
          <UButton label="Nuevo Repuesto" color="primary" icon="i-lucide-plus" @click="handleCreate" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="p-6">
        <div class="mb-6">
          <div class="flex items-center gap-3">
            <UIcon name="i-lucide-alert-triangle" class="text-warning text-3xl" />
            <div>
              <h2 class="text-2xl font-semibold">Stock Bajo</h2>
              <p class="text-sm text-muted mt-1">
                Se muestran {{ repuestos.data.length }} repuestos con stock igual o menor al minimo requerido
              </p>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-1.5 mb-4">
          <UInput v-model="globalFilter" class="w-full sm:w-1/2" size="xl" icon="i-lucide-search" placeholder="Buscar repuesto..." />
        </div>

        <UTable
          ref="table"
          v-model:row-selection="rowSelection"
          v-model:pagination="pagination"
          v-model:global-filter="globalFilter"
          :pagination-options="{ getPaginationRowModel: getPaginationRowModel() }"
          :filter-options="{ getFilteredRowModel: getFilteredRowModel() }"
          :data="repuestos.data"
          :columns="columns"
        />

        <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto">
          <div class="text-sm text-muted">Total: {{ repuestos.data.length }} repuestos con stock bajo</div>
          <UPagination
            :default-page="(table?.tableApi?.getState().pagination.pageIndex || 0) + 1"
            :items-per-page="table?.tableApi?.getState().pagination.pageSize"
            :total="table?.tableApi?.getFilteredRowModel().rows.length"
            @update:page="(p: number) => table?.tableApi?.setPageIndex(p - 1)"
          />
        </div>
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
