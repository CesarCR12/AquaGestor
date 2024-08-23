const elementsWithRipple = document.querySelectorAll('[ripple]');
elementsWithRipple.forEach(elementWithRipple => {
elementWithRipple.addEventListener('pointerdown', (mouseEvent) => {
    const rippleEl = document.createElement('div');
    rippleEl.classList.add('ripple');
    
    const x = mouseEvent.offsetX;
    const y = mouseEvent.offsetY;
    
    rippleEl.style.left = `${x}px`;
    rippleEl.style.top = `${y}px`;
    
    elementWithRipple.appendChild(rippleEl);
    
    requestAnimationFrame(() => {
    rippleEl.classList.add('run');
    });
    
    rippleEl.addEventListener('transitionend', () => {
    rippleEl.remove();
    });
});
})