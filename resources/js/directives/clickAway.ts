export const vClickAway = {
  mounted(el: HTMLElement, binding: any) {
    el.clickAwayEvent = function (event: Event) {
      if (!(el === event.target || el.contains(event.target as Node))) {
        binding.value(event)
      }
    }
    document.addEventListener('click', el.clickAwayEvent)
  },
  unmounted(el: HTMLElement) {
    document.removeEventListener('click', el.clickAwayEvent)
  }
}

declare module '@vue/runtime-core' {
  interface HTMLElement {
    clickAwayEvent?: (event: Event) => void
  }
}
