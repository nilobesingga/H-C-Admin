export default function registerGlobalComponents(app) {
    // Register other global components
    const components = import.meta.glob('./components/GlobalComponents/**/*.vue', { eager: true });

    for (const [path, definition] of Object.entries(components)) {
        const componentName = path
            .split('/')
            .pop()
            .replace(/\.\w+$/, '');
        app.component(componentName, definition.default);
    }
}
