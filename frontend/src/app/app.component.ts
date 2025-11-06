import { Component } from '@angular/core';

@Component({
  selector: 'app-root',
  template: `
    <nav class="navbar navbar-dark sticky-top">
      <div class="container">
        <a class="navbar-brand text-white fw-bold" routerLink="/">
          <span style="color: var(--primary);">E-Learning</span> Platform
        </a>
      </div>
    </nav>
    <router-outlet></router-outlet>
    <footer class="py-4 text-center" style="color: var(--muted); margin-top: 3rem;">
      <div class="container">
        <p class="mb-0">&copy; {{year}} E-Learning Platform. All rights reserved.</p>
      </div>
    </footer>
  `
})
export class AppComponent {
  year = new Date().getFullYear();
}
