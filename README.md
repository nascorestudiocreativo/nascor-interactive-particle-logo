# Nascor Interactive Particle Logo ✨

Plugin para WordPress que renderiza un logotipo utilizando HTML5 Canvas y lo descompone en un sistema de partículas interactivas. Las partículas se dispersan de forma fluida al interactuar con el cursor o toques en la pantalla y regresan a su forma original mediante un efecto elástico (Spring Physics).

## 🚀 Características Principales

*   **Física de Partículas Dinámica:** Implementa un algoritmo que calcula la distancia del cursor para aplicar una fuerza de repulsión y una fuerza de retorno tipo resorte para que la imagen se regenere suavemente.
*   **Soporte Táctil y Móvil Inteligente:** Incorpora eventos `touchmove` y `touchend` para dispositivos móviles. El tamaño del canvas y la densidad de las partículas (`step`) se reajustan de forma automática si la resolución de la pantalla es menor a 600px, garantizando un rendimiento óptimo[cite: 7].
*   **Precisión de Puntero:** Calcula dinámicamente las coordenadas del cursor utilizando `getBoundingClientRect()` y un factor de escala, evitando desfases provocados por el redimensionamiento CSS o el scroll[cite: 7].
*   **Soporte Cross-Origin:** Permite cargar imágenes alojadas en dominios externos de manera segura mediante el atributo `crossOrigin = "Anonymous"`[cite: 7].
*   **Limpieza de Animación:** Optimiza el uso de CPU cancelando fotogramas y reconstruyendo la matriz de partículas únicamente tras pausas de redimensionamiento de pantalla (mediante un `setTimeout` de 200ms)[cite: 7].

## 🛠️ Instalación

1. Descarga el repositorio como archivo `.zip`.
2. En tu panel de WordPress, navega a **Plugins > Añadir nuevo > Subir plugin**.
3. Sube el archivo y haz clic en **Instalar ahora**.
4. Activa el plugin **Nascor Interactive Particle Logo**[cite: 7].

## 💻 Uso del Shortcode

Para mostrar el logotipo interactivo, pega el siguiente shortcode en tu página, post o widget:

```text
[nascor_particle_logo]

Personalización de URL
Si deseas utilizar una imagen específica distinta a la configurada por defecto, puedes pasar el parámetro logo_url directamente en el shortcode[cite: 7]:

Plaintext
[nascor_particle_logo logo_url="[https://tu-sitio.com/tu-logo-personalizado.png](https://tu-sitio.com/tu-logo-personalizado.png)"]
👨‍💻 Autor y Versión
Autor: Nascor Estudio Creativo[cite: 7]

Versión: 2.0.1[cite: 7]

Sitio Web: Nascor.ar

[cite: 7]
