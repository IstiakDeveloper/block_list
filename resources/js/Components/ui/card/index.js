// resources/js/Components/ui/card/index.js
import { h } from 'vue'

const Card = {
  setup(props, { slots }) {
    return () =>
      h(
        'div',
        {
          class:
            'rounded-lg border border-gray-200 bg-white text-gray-950 shadow-sm dark:border-gray-800 dark:bg-gray-950 dark:text-gray-50',
        },
        slots.default?.()
      )
  },
}

const CardHeader = {
  setup(props, { slots }) {
    return () =>
      h('div', { class: 'flex flex-col space-y-1.5 p-6' }, slots.default?.())
  },
}

const CardTitle = {
  setup(props, { slots }) {
    return () =>
      h(
        'h3',
        {
          class:
            'text-2xl font-semibold leading-none tracking-tight',
        },
        slots.default?.()
      )
  },
}

const CardDescription = {
  setup(props, { slots }) {
    return () =>
      h('p', { class: 'text-sm text-gray-500 dark:text-gray-400' }, slots.default?.())
  },
}

const CardContent = {
  setup(props, { slots }) {
    return () => h('div', { class: 'p-6 pt-0' }, slots.default?.())
  },
}

const CardFooter = {
  setup(props, { slots }) {
    return () =>
      h('div', { class: 'flex items-center p-6 pt-0' }, slots.default?.())
  },
}

export { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter }
