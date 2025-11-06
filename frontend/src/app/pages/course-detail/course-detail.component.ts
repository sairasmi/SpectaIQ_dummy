import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ApiService, Course } from '../../services/api.service';
import { environment } from '../../../environments/environment';

declare var Razorpay: any;

@Component({
  selector: 'app-course-detail',
  templateUrl: './course-detail.component.html',
  styleUrls: ['./course-detail.component.css']
})
export class CourseDetailComponent implements OnInit {
  course: Course | null = null;
  loading = true;
  error = '';
  purchaseForm: FormGroup;
  showModal = false;
  processing = false;

  constructor(
    private route: ActivatedRoute,
    private apiService: ApiService,
    private fb: FormBuilder
  ) {
    this.purchaseForm = this.fb.group({
      name: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      mobile: ['', [Validators.required, Validators.pattern(/^\d{10}$/)]],
      whatsapp: ['']
    });
  }

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadCourse(parseInt(id));
    }
  }

  loadCourse(id: number) {
    this.loading = true;
    this.apiService.getCourse(id).subscribe({
      next: (course) => {
        this.course = course;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error loading course:', err);
        this.error = 'Failed to load course details.';
        this.loading = false;
      }
    });
  }

  openPurchaseModal() {
    this.showModal = true;
  }

  closeModal() {
    this.showModal = false;
    this.purchaseForm.reset();
  }

  formatPrice(price: number): string {
    return (price / 100).toFixed(2);
  }

  async submitPurchase() {
    if (this.purchaseForm.invalid || !this.course) {
      return;
    }

    this.processing = true;
    const formData = this.purchaseForm.value;
    
    try {
      const preorderData = {
        product_type: 'course',
        product_id: this.course.id,
        name: formData.name,
        email: formData.email,
        mobile: formData.mobile,
        whatsapp: formData.whatsapp || formData.mobile,
        amount: this.course.price,
        currency: this.course.currency
      };

      const response = await this.apiService.createPreorder(preorderData).toPromise();
      
      if (response) {
        this.openRazorpayCheckout(response);
      }
    } catch (error) {
      console.error('Error creating preorder:', error);
      alert('Failed to initiate payment. Please try again.');
      this.processing = false;
    }
  }

  openRazorpayCheckout(orderData: any) {
    const options = {
      key: orderData.key_id,
      amount: orderData.amount,
      currency: orderData.currency,
      name: 'E-Learning Platform',
      description: this.course?.title || 'Course Purchase',
      order_id: orderData.razorpay_order_id,
      prefill: {
        name: this.purchaseForm.value.name,
        email: this.purchaseForm.value.email,
        contact: this.purchaseForm.value.mobile
      },
      theme: {
        color: '#6366F1'
      },
      handler: (response: any) => {
        this.handlePaymentSuccess(response, orderData.razorpay_order_id);
      },
      modal: {
        ondismiss: () => {
          this.processing = false;
        }
      }
    };

    const rzp = new Razorpay(options);
    rzp.open();
  }

  async handlePaymentSuccess(response: any, orderId: string) {
    try {
      const status = await this.apiService.getOrderStatus(orderId);
      alert('Payment successful! You will receive an email with course access details.');
      this.closeModal();
      this.processing = false;
    } catch (error) {
      console.error('Error checking order status:', error);
      alert('Payment completed! Please check your email for access details.');
      this.closeModal();
      this.processing = false;
    }
  }
}
