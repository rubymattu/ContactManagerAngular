import { Component, ChangeDetectorRef } from '@angular/core';
import { Auth } from '../../services/auth';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { FormsModule, NgForm } from '@angular/forms';

@Component({
  selector: 'app-login',
  templateUrl: './login.html',
  styleUrls: ['./login.css'],
  imports: [HttpClientModule, CommonModule, FormsModule, RouterModule],
})
export class Login {
  userName= ''; 
  password= '' ;
  errorMessage = '';
  successMessage = '';

  constructor(private auth: Auth, private router: Router, private cdr: ChangeDetectorRef) {}

  login() {

    this.auth.login({ userName: this.userName, password: this.password }).subscribe({
      next: res => {
        if (res.success) {
          this.auth.setAuth(true);
          localStorage.setItem('username', this.userName);
          this.router.navigate(['/contacts']);
          this.cdr.detectChanges();
        } else {
          this.errorMessage = res.message;
          this.cdr.detectChanges();
        }
      },
      error: (err) => {
        if (err.status === 403) {
          // Lockout case
          this.errorMessage = 'Too many failed attempts. ' + (err.error?.error || 'Please wait 5 minutes before trying again.');
          this.cdr.detectChanges(); 
        } else if (err.status === 401) {
          this.errorMessage = 'Invalid username or password.';
          this.cdr.detectChanges();
        } else if (err.status === 404) {
          this.errorMessage = 'User not found.';
          this.cdr.detectChanges();
        } else {
          this.errorMessage = 'An unexpected error occurred. Please try again.';
          this.cdr.detectChanges();
        }
      }
    });
  }
}
