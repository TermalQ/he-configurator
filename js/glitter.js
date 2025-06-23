document.addEventListener('DOMContentLoaded', function() {
    const glitterCheckbox = document.getElementById('glitter'); // Switch

    const glitterLayer = document.getElementById('glitter-layer'); // SVG

    glitterCheckbox.addEventListener('change', function() {
        if (this.checked) {
            createGlitter(glitterLayer, 20000); // n-elementów
        } else {
            while (glitterLayer.firstChild) {
                glitterLayer.firstChild.remove(); // Usuń wszystkie elementy glitter, gdy checkbox jest odznaczony
            }
        }
    });

});


const blink = 1; // migotanie

function getRandomGlitterColor() {
  const colors = ["#FFFFFF", "#FFFF99", "#FFD700", "#FFA500", "#FFED00", "#FFFF00", "#FFFFFF"]; // Biały, jasnożółty, złoty, pomarańczowy
  return colors[Math.floor(Math.random() * colors.length)];
}

function createGlitter(targetElement, numSparkles) {
  for (let i = 0; i < numSparkles; i++) {
    const x = Math.random() * 300;
    const y = Math.random() * 300;
    const fillColor = getRandomGlitterColor();
    const opacity = Math.random() * 0.25 + 0.25;

    const shapeType = Math.random() < 0.33 ? 'star' : 'ellipse';

    if (shapeType === 'star') {
      // 1/3 elementów to gwiazdki
      const star = document.createElementNS("http://www.w3.org/2000/svg", "polygon");
      star.setAttribute("points", "2,0 4,4 8,4 5,7 6,11 2,9 0,11 1,7 -2,4 2,4");
      star.setAttribute("fill", fillColor);
      star.setAttribute("opacity", opacity);
      
      // Losowy rozmiar gwiazdek (mniejsze niż wcześniej)
      const scale = Math.random() * 0.035 + 0.01; // 0.1 do 0.7
      star.setAttribute("transform", `translate(${x},${y}) scale(${scale})`);
      
      if (blink && Math.random() < 0.1) { // 10% gwiazdek miga
        star.classList.add("sparkle");
        star.style.setProperty("--blink-time", `${(Math.random() * 4 + 1).toFixed(2)}s`); // 1s do 5s
      }
      targetElement.appendChild(star);
    } else {
      // Reszta to plamki
      const ellipse = document.createElementNS("http://www.w3.org/2000/svg", "ellipse");
      ellipse.setAttribute("cx", x);
      ellipse.setAttribute("cy", y);
      ellipse.setAttribute("rx", Math.random() * 0.35 + 0.025);
      ellipse.setAttribute("ry", Math.random() * 0.25 + 0.03);
      ellipse.setAttribute("fill", fillColor);
      ellipse.setAttribute("opacity", opacity);

      if (blink && Math.random() < 0.1) { // 10% plamek miga
        ellipse.classList.add("sparkle");
        ellipse.style.setProperty("--blink-time", `${(Math.random() * 4 + 1).toFixed(2)}s`); // 1s do 5s
      }

      targetElement.appendChild(ellipse);
    }
  }
}