export default function registerAuthComponents(app) {
    const components = import.meta.glob('./components/Auth/**/*.vue', { eager: true });

    for (const [path, definition] of Object.entries(components)) {
        const componentName = path
            .split('/')
            .pop()
            .replace(/\.\w+$/, '');
        app.component(componentName, definition.default);
    }
}
