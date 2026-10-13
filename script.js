//carousel
document.addEventListener('DOMContentLoaded', () => {
    const grid = document.querySelector('.protagonistas-grid');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');

    if (!grid || !prevBtn || !nextBtn) return;


    nextBtn.addEventListener('click', () => {
        const tarjetas = grid.querySelectorAll('.p-card');
        if (tarjetas.length > 0) {
            grid.appendChild(tarjetas[0]);
        }
    });

    prevBtn.addEventListener('click', () => {
        const tarjetas = grid.querySelectorAll('.p-card');
        if (tarjetas.length > 0) {
            grid.insertBefore(tarjetas[tarjetas.length - 1], tarjetas[0]);
        }
    });
});

//to top
const toTop = document.querySelector(".to-top");

document.addEventListener("scroll", () => {
    let scrollY = window.scrollY;
    if (scrollY > 150) toTop.classList.add("visible");
    else toTop.classList.remove("visible");

})

//negritas, italicas, etc
function insertFormat(format, event) {
    if (event) event.preventDefault();

    const textarea = document.querySelector('.text-editor');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);

    let replacement = '';
    let defaultText = '';

    switch (format) {
        case 'bold':
            defaultText = 'texto en negrita';
            replacement = `**${selectedText || defaultText}**`;
            break;
        case 'italic':
            defaultText = 'texto en cursiva';
            replacement = `*${selectedText || defaultText}*`;
            break;
        case 'link':
            defaultText = 'enlace';
            replacement = `[${selectedText || defaultText}](https://ejemplo.com)`;
            break;
        case 'list':
            defaultText = 'Elemento de lista';
            replacement = `\n- ${selectedText || defaultText}`;
            break;
    }

    textarea.setRangeText(replacement, start, end, 'select');
    textarea.focus();


    if (!selectedText) {
        const newStart = start + (format === 'bold' ? 2 : format === 'italic' ? 1 : format === 'link' ? 1 : 3);
        const newEnd = newStart + defaultText.length;
        textarea.setSelectionRange(newStart, newEnd);
    }
}

