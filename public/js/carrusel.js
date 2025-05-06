const datos = [
    {
        imagen: 'img/banner/todoEn.jpg',
        frase: '"En otra vida, me hubiera gustado lavar la ropa y hacer los impuestos contigo"',
        autor: '- Everything Everywhere All at Once (2022)'
    },
    {
        imagen: 'img/banner/coraline.jpg',
        frase: '"Probablemente crees que este mundo es un sueño hecho realidad... pero estás equivocada."',
        autor: '- Gato (Coraline)'
    },
    {
        imagen: 'img/banner/lalaland.jpg',
        frase: '"Porque cuando me mirabas, todo el mundo desaparecía."',
        autor: '- Sebastian'
    },
    {
        imagen: 'img/banner/cronica.jpg',
        frase: '"Te amo, pero no tienes idea de lo que estás diciendo."',
        autor: '- Lucinda Krementz'
    },
    {
        imagen: 'img/hero.jpg',
        frase: '"Ya no quiero vivir en un agujero"',
        autor: '- Mr. Fox'
    }
];

let cont = 0;

document.addEventListener('DOMContentLoaded', () => {
    const atras = document.querySelector('.atras');
    const adelante = document.querySelector('.adelante');
    const frase = document.querySelector('.frase');
    const autor = document.querySelector('.autor');
    const imagen = document.querySelector('.imagen-hero');

    function actualizarCarrusel() {
        imagen.style.opacity = 0;
        setTimeout(() => {
            imagen.src = datos[cont].imagen;
            frase.textContent = datos[cont].frase;
            autor.textContent = datos[cont].autor;
            imagen.style.opacity = 1;
        }, 300);
    }

    atras.addEventListener('click', () => {
        cont = (cont - 1 + datos.length) % datos.length;
        actualizarCarrusel();
    });

    adelante.addEventListener('click', () => {
        cont = (cont + 1) % datos.length;
        actualizarCarrusel();
    });

    setInterval(() => {
        cont = (cont + 1) % datos.length;
        actualizarCarrusel();
    }, 5000);
});
