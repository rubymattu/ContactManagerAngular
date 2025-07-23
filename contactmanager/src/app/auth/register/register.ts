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
  styleUrl: './register.css'
})

export class Register {
  userName = '';
  password = '';
  emailAddress = '';
  errorMessage = '';
  successMessage = '';

  constructor(private auth: Auth, private router: Router, private cdr: ChangeDetectorRef ) {}

  register() {
    this.auth.register({
      userName: this.userName,
      password: this.password,
      emailAddress: this.emailAddress
    }).subscribe({
      next: res => {
        if (res.success) {
          this.auth.setAuth(true);
          this.successMessage = 'Registration successful. Please log in.';
          setTimeout(() => this.router.navigate(['/login']), 1500);
        } else {
          this.errorMessage = res.message;
          this.cdr.detectChanges(); // Ensure UI updates with error message

        }
      },
      error: () => this.errorMessage = 'Server error during registration.'
    });
  }
}

