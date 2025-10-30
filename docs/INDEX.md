<a id="snackshop-indice-maestro-del-manual-tecnico"></a>
<a id="-snackshop-indice-maestro-del-manual-tecnico"></a>
# 📖 SnackShop — Índice maestro del Manual Técnico
<!-- TOC -->
<a id="contenido"></a>
<a id="-contenido"></a>
## Contenido

- [🧭 Navegación Rápida](#-navegacion-rapida)
- [📚 Estructura Completa del Manual](#-estructura-completa-del-manual)
  - [Nivel 1: Fundamentos](#nivel-1-fundamentos)
  - [Nivel 2: Referencias Técnicas](#nivel-2-referencias-tecnicas)
  - [Nivel 3: Operaciones y Despliegue](#nivel-3-operaciones-y-despliegue)
  - [Nivel 4: Desarrollo](#nivel-4-desarrollo)
  - [Nivel 5: Recursos Específicos](#nivel-5-recursos-especificos)
- [🎯 Guías por Perfil de Usuario](#-guias-por-perfil-de-usuario)
  - [👨‍💻 Para desarrolladores nuevos](#-para-desarrolladores-nuevos)
  - [🔧 Para Administradores de Sistema](#-para-administradores-de-sistema)
  - [🚀 Para integraciones (API)](#-para-integraciones-api)
  - [🧪 Para Testing y QA](#-para-testing-y-qa)
- [📖 Convenciones del Manual](#-convenciones-del-manual)
  - [Estructura de Cada Documento](#estructura-de-cada-documento)
- [🧭 Navegación](#-navegacion)
- [📋 Índice Interno](#-indice-interno)
- [Sección 1](#seccion-1)
- [Sección 2](#seccion-2)
- [Contenido...](#contenido)
- [🔗 Documentos Relacionados](#-documentos-relacionados)
- [📞 Soporte](#-soporte)
  - [Iconos y Convenciones](#iconos-y-convenciones)
  - [Estados de Documentación](#estados-de-documentacion)
- [🔗 Enlaces Externos Útiles](#-enlaces-externos-utiles)
  - [Recursos de PHP](#recursos-de-php)
  - [Herramientas de Desarrollo](#herramientas-de-desarrollo)
  - [Base de Datos](#base-de-datos)
- [📞 Soporte y Contribuciones](#-soporte-y-contribuciones)
  - [Reportar Problemas](#reportar-problemas)
  - [Contribuir al Manual](#contribuir-al-manual)
  - [Contacto](#contacto)
<!-- /TOC -->

> **Ubicación:** `docs/INDEX.md` • **Última actualización:** 30 de octubre, 2025

Breve: este índice es la puerta de entrada para desarrolladores y operadores — contiene enlaces rápidos, el estado de cada sección y rutas a ejemplos y guías operativas. Usa la columna "Ir a" para saltar al documento correspondiente.

---

<a id="navegacion-rapida"></a>
<a id="-navegacion-rapida"></a>
## 🧭 Navegación Rápida

| Documento | Estado | Descripción | Ir a |
|-----------|--------|-------------|------|
| **Inicio** | ✅ | Guía de instalación y conceptos básicos | [README.md](README.md) |
| **Arquitectura** | ✅ | Patrones, capas y flujo de datos | [ARCHITECTURE.md](ARCHITECTURE.md) |
| **API** | ✅ | Endpoints, rutas y ejemplos de uso | [API.md](API.md) |
| **Base de Datos** | ✅ | Esquema, migraciones y queries | [DATABASE.md](DATABASE.md) |
| **Despliegue** | ✅ | Docker, servidores y producción | [DEPLOYMENT.md](DEPLOYMENT.md) |
| **Desarrollo** | ✅ | Setup avanzado y debugging | [DEVELOPMENT.md](DEVELOPMENT.md) |
| **Testing** | ✅ | Pruebas y calidad de código | [TESTING.md](TESTING.md) |
| **Seguridad** | ✅ | Vulnerabilidades y protecciones | [SECURITY.md](SECURITY.md) |

---

<a id="estructura-completa-del-manual"></a>
<a id="-estructura-completa-del-manual"></a>
## 📚 Estructura Completa del Manual

<a id="nivel-1-fundamentos"></a>
<a id="-nivel-1-fundamentos"></a>
### Nivel 1: Fundamentos

```
📂 Conceptos básicos y arquitectura
├── README.md ..................... Guía rápida de inicio
├── ARCHITECTURE.md ............... Patrones y diseño del sistema
└── docs/INDEX.md ................. Este índice maestro
```

<a id="nivel-2-referencias-tecnicas"></a>
<a id="-nivel-2-referencias-tecnicas"></a>
### Nivel 2: Referencias Técnicas

```
📂 APIs, base de datos y configuración
├── API.md ........................ Documentación completa de endpoints
├── DATABASE.md ................... Esquema, queries y migraciones
└── CONFIGURATION.md .............. Variables de entorno y configuración
```

<a id="nivel-3-operaciones-y-despliegue"></a>
<a id="-nivel-3-operaciones-y-despliegue"></a>
### Nivel 3: Operaciones y Despliegue

```
📂 Producción y mantenimiento
├── DEPLOYMENT.md ................. Guías de despliegue completas
├── SECURITY.md ................... Medidas de seguridad implementadas
└── PERFORMANCE.md ................ Optimización y escalabilidad
```

<a id="nivel-4-desarrollo"></a>
<a id="-nivel-4-desarrollo"></a>
### Nivel 4: Desarrollo

```
📂 Herramientas para desarrolladores
├── DEVELOPMENT.md ................ Setup avanzado y workflows
├── TESTING.md .................... Estrategias de testing
├── CONTRIBUTING.md ............... Guía para contribuidores
└── EXTENDING.md .................. Cómo extender el sistema
```

<a id="nivel-5-recursos-especificos"></a>
<a id="-nivel-5-recursos-especificos"></a>
### Nivel 5: Recursos Específicos

```
📂 Documentación detallada por componente
└── docs/
    ├── services/ ................. Documentación de cada service
    │   ├── CostoService.md
    │   ├── ImpuestosService.md
    │   ├── ImageProcessingService.md
    │   ├── SaleService.md
    │   └── UserService.md
    ├── examples/ ................. Ejemplos de código prácticos
    │   ├── new-controller-example.php
    │   ├── custom-service-example.php
    │   └── api-integration-example.php
    └── diagrams/ ................. Diagramas y flujos
        ├── architecture.txt
        ├── database-erd.txt
        └── request-flow.txt
```

---

<a id="guias-por-perfil-de-usuario"></a>
<a id="-guias-por-perfil-de-usuario"></a>
## 🎯 Guías por Perfil de Usuario

<a id="para-desarrolladores-nuevos"></a>
<a id="-para-desarrolladores-nuevos"></a>
### 👨‍💻 Para desarrolladores nuevos

Ruta recomendada para empezar:

1. **[README.md](README.md)** — Instalación y primer arranque
2. **[ARCHITECTURE.md](ARCHITECTURE.md)** — Estructura y patrones
3. **[DEVELOPMENT.md](DEVELOPMENT.md)** — Setup, comandos y workflows
4. **[API.md](API.md)** — Endpoints y ejemplos de uso
5. **[docs/examples/](examples/)** — Ejemplos prácticos (scripts y tests)

<a id="para-administradores-de-sistema"></a>
<a id="-para-administradores-de-sistema"></a>
### 🔧 Para Administradores de Sistema

**Ruta recomendada para despliegue:**

1. **[README.md](README.md)** — Requisitos básicos
2. **[DEPLOYMENT.md](DEPLOYMENT.md)** — Despliegue en producción
3. **[DATABASE.md](DATABASE.md)** — Configuración de BD
4. **[SECURITY.md](SECURITY.md)** — Medidas de seguridad
5. **[TROUBLESHOOTING.md](TROUBLESHOOTING.md)** — Solución de problemas

<a id="para-integraciones-api"></a>
<a id="-para-integraciones-api"></a>
### 🚀 Para integraciones (API)

Ruta recomendada para integraciones:

1. **[API.md](API.md)** — Documentación completa de endpoints
2. **[ARCHITECTURE.md](ARCHITECTURE.md)** — Flujo de autenticación y decisiones de diseño
3. **[docs/examples/api-integration-example.php](examples/api-integration-example.php)** — Script de ejemplo para consumo de la API
4. **[SECURITY.md](SECURITY.md)** — Recomendaciones de seguridad y tokens

<a id="para-testing-y-qa"></a>
<a id="-para-testing-y-qa"></a>
### 🧪 Para Testing y QA

**Ruta recomendada para pruebas:**

1. **[TESTING.md](TESTING.md)** — Estrategias de testing
2. **[DEVELOPMENT.md](DEVELOPMENT.md)** — Setup de entorno de pruebas
3. **[docs/examples/testing-example.php](examples/testing-example.php)** — Ejemplos de tests
4. **[DATABASE.md](DATABASE.md)** — Datos de prueba y seeds

---

<a id="convenciones-del-manual"></a>
<a id="-convenciones-del-manual"></a>
## 📖 Convenciones del Manual

<a id="estructura-de-cada-documento"></a>
<a id="-estructura-de-cada-documento"></a>
### Estructura de Cada Documento

Todos los documentos del manual siguen esta estructura estándar:

```markdown
<a id="titulo-del-documento"></a>
<a id="-titulo-del-documento"></a>
# 📄 Título del Documento

**🏠 Ubicación:** ruta/del/archivo.md
**📅 Última actualización:** fecha
**🎯 Propósito:** descripción breve

---

<a id="navegacion"></a>
<a id="-navegacion"></a>
## 🧭 Navegación
[enlaces a documentos relacionados]

<a id="indice-interno"></a>
<a id="-indice-interno"></a>
## 📋 Índice Interno
- [Sección 1](#sección-1)
- [Sección 2](#sección-2)

<a id="seccion-1"></a>
<a id="-seccion-1"></a>
## Sección 1

Contenido de ejemplo para la Sección 1 (plantilla). Puedes editar o eliminar esta sección según el manual real.

<a id="seccion-2"></a>
<a id="-seccion-2"></a>
## Sección 2

Contenido de ejemplo para la Sección 2 (plantilla). Sirve como ejemplo de índice interno.

---

<a id="contenido"></a>
<a id="-contenido"></a>
## Contenido...

---

<a id="documentos-relacionados"></a>
<a id="-documentos-relacionados"></a>
## 🔗 Documentos Relacionados
[enlaces a otros documentos del manual]

<a id="soporte"></a>
<a id="-soporte"></a>
## 📞 Soporte
[información de contacto/issues]
```

<a id="iconos-y-convenciones"></a>
<a id="-iconos-y-convenciones"></a>
### Iconos y Convenciones

- 📖 **Documentación general**
- 🏗️ **Arquitectura y diseño**
- 🔌 **APIs y endpoints**
- 🗄️ **Base de datos**
- 🚀 **Despliegue y producción**
- 🔧 **Desarrollo y debugging**
- 🧪 **Testing y calidad**
- 🔒 **Seguridad**
- 📊 **Performance y métricas**
- 🎯 **Ejemplos y tutoriales**

<a id="estados-de-documentacion"></a>
<a id="-estados-de-documentacion"></a>
### Estados de Documentación

- ✅ **Completo** — Documentación finalizada y revisada
- 🚧 **En progreso** — En desarrollo
- 📝 **Planificado** — Por implementar
- ⚠️ **Necesita actualización** — Requiere revisión

---

<a id="enlaces-externos-utiles"></a>
<a id="-enlaces-externos-utiles"></a>
## 🔗 Enlaces Externos Útiles

<a id="recursos-de-php"></a>
<a id="-recursos-de-php"></a>
### Recursos de PHP

- [PHP Manual](https://www.php.net/manual/) — Documentación oficial de PHP
- [PSR Standards](https://www.php-fig.org/psr/) — PHP Standards Recommendations
- [Composer](https://getcomposer.org/doc/) — Gestión de dependencias

<a id="herramientas-de-desarrollo"></a>
<a id="-herramientas-de-desarrollo"></a>
### Herramientas de Desarrollo

- [PHPUnit](https://phpunit.de/documentation.html) — Framework de testing
- [Xdebug](https://xdebug.org/docs/) — Debugging y profiling
- [Docker](https://docs.docker.com/) — Contenedores

<a id="base-de-datos"></a>
<a id="-base-de-datos"></a>
### Base de Datos

- [MySQL Documentation](https://dev.mysql.com/doc/) — Documentación oficial de MySQL
- [PDO Manual](https://www.php.net/manual/en/book.pdo.php) — PHP Data Objects

---

<a id="soporte-y-contribuciones"></a>
<a id="-soporte-y-contribuciones"></a>
## 📞 Soporte y Contribuciones

<a id="reportar-problemas"></a>
<a id="-reportar-problemas"></a>
### Reportar Problemas

- **Issues del proyecto:** [GitHub Issues](https://github.com/Equinoxe-Grammer/SnackShack/issues)
- **Documentación incorrecta:** Crear issue con etiqueta `documentation`

<a id="contribuir-al-manual"></a>
<a id="-contribuir-al-manual"></a>
### Contribuir al Manual

1. Lee **[CONTRIBUTING.md](CONTRIBUTING.md)** para las guías de contribución
2. Sigue las convenciones de documentación de esta página
3. Actualiza este índice si añades nuevos documentos

<a id="contacto"></a>
<a id="-contacto"></a>
### Contacto

- **Repositorio:** [SnackShack](https://github.com/Equinoxe-Grammer/SnackShack)
- **Propietario:** Equinoxe-Grammer
- **Rama principal:** `main`

---

**🏠 [Volver al inicio](README.md)** | **🏗️ [Ver Arquitectura](ARCHITECTURE.md)** | **🔌 [Ver API](API.md)**
