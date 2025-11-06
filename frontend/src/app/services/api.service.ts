import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, firstValueFrom } from 'rxjs';
import { environment } from '../../environments/environment';

export interface Course {
  id: number;
  title: string;
  summary: string;
  price: number;
  currency: string;
  cover_url?: string;
  status: string;
}

export interface PreorderResponse {
  preorder_id: number;
  razorpay_order_id: string;
  amount: number;
  currency: string;
  key_id: string;
}

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private baseUrl = environment.apiBase;

  constructor(private http: HttpClient) {}

  getCourses(): Observable<{data: Course[]}> {
    return this.http.get<{data: Course[]}>(`${this.baseUrl}/courses`);
  }

  getCourse(id: number): Observable<Course> {
    return this.http.get<Course>(`${this.baseUrl}/courses/${id}`);
  }

  createPreorder(payload: any): Observable<PreorderResponse> {
    return this.http.post<PreorderResponse>(`${this.baseUrl}/preorders`, payload);
  }

  async getOrderStatus(orderId: string): Promise<{state: string}> {
    return firstValueFrom(this.http.get<{state: string}>(`${this.baseUrl}/orders/${orderId}/status`));
  }
}
