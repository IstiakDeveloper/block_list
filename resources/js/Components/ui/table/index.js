//resources/js/Components/ui/table/index.js
import { h } from 'vue'

const Table = {
  setup(props, { slots }) {
    return () =>
      h(
        'div',
        {
          class:
            'relative w-full overflow-auto',
        },
        h(
          'table',
          {
            class:
              'w-full caption-bottom text-sm',
          },
          slots.default?.()
        )
      )
  },
}

const TableHeader = {
  setup(props, { slots }) {
    return () =>
      h('thead', { class: 'border-b border-gray-200 dark:border-gray-800' }, slots.default?.())
  },
}

const TableBody = {
  setup(props, { slots }) {
    return () =>
      h('tbody', { class: 'divide-y divide-gray-200 dark:divide-gray-800' }, slots.default?.())
  },
}

const TableRow = {
  setup(props, { slots }) {
    return () =>
      h(
        'tr',
        {
          class:
            'hover:bg-gray-100/50 dark:hover:bg-gray-800/50 data-[state=selected]:bg-gray-100 dark:data-[state=selected]:bg-gray-800',
        },
        slots.default?.()
      )
  },
}

const TableHead = {
  setup(props, { slots }) {
    return () =>
      h(
        'th',
        {
          class:
            'h-12 px-4 text-left align-middle font-medium text-gray-500 dark:text-gray-400 [&:has([role=checkbox])]:pr-0',
        },
        slots.default?.()
      )
  },
}

const TableCell = {
  setup(props, { slots }) {
    return () =>
      h(
        'td',
        {
          class:
            'p-4 align-middle [&:has([role=checkbox])]:pr-0',
        },
        slots.default?.()
      )
  },
}

export { Table, TableHeader, TableBody, TableRow, TableHead, TableCell }
