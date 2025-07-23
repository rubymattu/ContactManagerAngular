import { Component } from '@angular/core';
import { Auth } from '../../services/auth';
import { RouterModule, Router } from '@angular/router';
import { FormsModule, NgForm } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { HttpClientModule, HttpClient } from '@angular/common/http';
import { ChangeDetectorRef } from '@angular/core';

@Component({
  standalone: true,
  selector: 'app-register',
  imports: [CommonModule, FormsModule, HttpClientModule, RouterModule],
  templateUrl: './register.html',
  styleUrl: './register.css',
  providers: [Auth]
})

export class Register {
  userName = '';
  password = '';
  emailAddress = '';
  errorMessage = '';
  successMessage = '';

  constructor(private auth: Auth, private router: Router, private cdr: ChangeDetectorRef ) {}

  
  register() {
    const trimmedUsername = this.userName.trim();
    const trimmedPassword = this.password.trim();
    const trimmedEmail = this.emailAddress.trim();
  
    // Basic front-end validation
    if (!trimmedUsername || !trimmedPassword || !trimmedEmail) {
      this.errorMessage = 'All fields are required.';
      this.successMessage = '';
      return;
    }
  
    // Call backend registration
    this.auth.register({
      userName: trimmedUsername,
      password: trimmedPassword,
      emailAddress: trimmedEmail
    }).subscribe({
      next: res => {
        if (res.success) {
          this.successMessage = 'Registration successful. Please log in.';
          this.errorMessage = '';
          setTimeout(() => this.router.navigate(['/login']), 1500);
        } else {
          this.errorMessage = res.message;
          this.successMessage = '';
        }
      },
      error: () => {
        this.errorMessage = 'Server error during registration.';
        this.successMessage = '';
      }
    });
  }
}

