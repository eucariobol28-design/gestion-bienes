# TODO - Mejorar vista Login

- [ ] Entender cómo se renderiza la vista login y por qué hereda el layout general.
- [x] Actualizar `app/Core/Router.php` para que `auth/login` no cargue `Views/layouts/header.php`/`footer.php`.
- [x] Crear layout(s) dedicados para login (`app/Views/layouts/login_header.php` y `login_footer.php`).
- [ ] Probar manualmente `/auth/login` y verificar que el diseño ya no depende del layout general.
- [ ] Confirmar que las demás rutas siguen usando el layout actual.


