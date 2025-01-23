const container = document.querySelector('.container');
const imageWrapper = document.querySelector('.image-wrapper');

container.addEventListener('mousemove', (e) => {
  const { clientX, clientY, currentTarget } = e;
  const { left, top, width, height } = currentTarget.getBoundingClientRect();

  const x = ((clientX - left) / width - 0.5) * 2;
  const y = ((clientY - top) / height - 0.5) * -2;

  const rotationX = y * 10;
  const rotationY = x * 10;

  imageWrapper.style.transform = `rotateX(${rotationX}deg) rotateY(${rotationY}deg)`;
});

container.addEventListener('mouseleave', () => {
  imageWrapper.style.transform = '';
});
