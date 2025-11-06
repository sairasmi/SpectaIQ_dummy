import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService, Course } from '../../services/api.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
  courses: Course[] = [];
  loading = true;
  error = '';

  constructor(
    private apiService: ApiService,
    private router: Router
  ) {}

  ngOnInit() {
    this.loadCourses();
  }

  loadCourses() {
    this.loading = true;
    this.apiService.getCourses().subscribe({
      next: (response) => {
        this.courses = response.data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error loading courses:', err);
        this.error = 'Failed to load courses. Please try again.';
        this.loading = false;
      }
    });
  }

  viewCourse(courseId: number) {
    this.router.navigate(['/courses', courseId]);
  }

  formatPrice(price: number): string {
    return (price / 100).toFixed(2);
  }
}
