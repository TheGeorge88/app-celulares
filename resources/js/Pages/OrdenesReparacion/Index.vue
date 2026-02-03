<script setup lang="ts">
import { ref, h, resolveComponent } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { TableColumn } from '@nuxt/ui'
import { getPaginationRowModel, getFilteredRowModel } from '@tanstack/table-core'
import type { Row } from '@tanstack/table-core'

interface OrdenReparacion {
  id: string
  codigoSeguimiento: string
  cliente?: { razonSocial: string }
  equipo?: { marca: string; modelo: string }
  tecnico?: { nombreCompleto: string }
  estado: string
  estadoDescripcion: string
  costoEstimado?: number
  costoFinal?: number
  autorizado: boolean
  createdAt: string
}

const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UBadge = resolveComponent('UBadge')

const props = defineProps<{
  ordenes: { data: OrdenReparacion[] }
}>()

const toast = useToast()
const table = useTemplateRef('table')
const globalFilter = ref('')
const rowSelection = ref({})

const handleCreate = () => router.visit(route('ordenes-reparacion.create'))
const handleView = (orden: OrdenReparacion) => router.visit(route('ordenes-reparacion.show', orden.id))
const handleEdit = (orden: OrdenReparacion) => router.visit(route('ordenes-reparacion.edit', orden.id))

const getEstadoColor = (estado: string) => {
  const colores: Record<string, string> = {
    'RECIBIDO': 'info',
    'EN_DIAGNOSTICO': 'warning',
    'PENDIENTE_AUTORIZACION': 'orange',
    'AUTORIZADO': 'teal',
    'EN_REPARACION': 'yellow',
    'REPARADO': 'success',
    'ENTREGADO': 'neutral',
    'CANCELADO': 'error'
  }
  return colores[estado] || 'neutral'
}

function getRowItems(row: Row<OrdenReparacion>) {
  return [
    { type: 'label', label: 'Acciones' },
    { label: 'Ver detalles', icon: 'i-lucide-eye', onSelect() { handleView(row.original) } },
    { label: 'Editar orden', icon: 'i-lucide-pencil', onSelect() { handleEdit(row.original) } },
    { type: 'separator' },
    { label: 'Copiar codigo', icon: 'i-lucide-copy', onSelect() {
      navigator.clipboard.writeText(row.original.codigoSeguimiento)
      toast.add({ title: 'Codigo copiado' })
    }}
  ]
}

const columns: TableColumn<OrdenReparacion>[] = [
  {
    accessorKey: 'codigoSeguimiento',
    header: 'Codigo',
    cell: ({ row }) => h('div', [
      h('p', { class: 'font-mono font-medium text-primary' }, row.original.codigoSeguimiento),
      h('p', { class: 'text-xs text-muted' }, row.original.createdAt)
    ])
  },
  {
    accessorKey: 'cliente',
    header: 'Cliente',
    cell: ({ row }) => row.original.cliente?.razonSocial || '-'
  },
  {
    accessorKey: 'equipo',
    header: 'Equipo',
    cell: ({ row }) => row.original.equipo ? `${row.original.equipo.marca} ${row.original.equipo.modelo}` : '-'
  },
  {
    accessorKey: 'tecnico',
    header: 'Tecnico',
    cell: ({ row }) => row.original.tecnico?.nombreCompleto || 'Sin asignar'
  },
  {
    accessorKey: 'estado',
    header: 'Estado',
    cell: ({ row }) => h(UBadge, {
      color: getEstadoColor(row.original.estado),
      variant: 'subtle'
    }, () => row.original.estadoDescripcion)
  },
  {
    accessorKey: 'costoEstimado',
    header: 'Costo Est.',
    cell: ({ row }) => row.original.costoEstimado ? `$${row.original.costoEstimado.toFixed(2)}` : '-'
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
  <UDashboardPanel id="ordenes-reparacion">
    <template #header>
      <UDashboardNavbar title="Ordenes de Reparacion">
        <template #leading><UDashboardSidebarCollapse /></template>
        <template #right>
          <UButton label="Nueva Orden" color="primary" icon="i-lucide-plus" @click="handleCreate" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5 mb-4">
        <UInput v-model="globalFilter" class="w-full sm:w-1/2" size="xl" icon="i-lucide-search" placeholder="Buscar orden..." />
      </div>

      <UTable
        ref="table"
        v-model:row-selection="rowSelection"
        v-model:pagination="pagination"
        v-model:global-filter="globalFilter"
        :pagination-options="{ getPaginationRowModel: getPaginationRowModel() }"
        :filter-options="{ getFilteredRowModel: getFilteredRowModel() }"
        :data="ordenes.data"
        :columns="columns"
      />

      <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto">
        <div class="text-sm text-muted">Total: {{ ordenes.data.length }} ordenes</div>
        <UPagination
          :default-page="(table?.tableApi?.getState().pagination.pageIndex || 0) + 1"
          :items-per-page="table?.tableApi?.getState().pagination.pageSize"
          :total="table?.tableApi?.getFilteredRowModel().rows.length"
          @update:page="(p: number) => table?.tableApi?.setPageIndex(p - 1)"
        />
      </div>
    </template>
  </UDashboardPanel>
</template>
