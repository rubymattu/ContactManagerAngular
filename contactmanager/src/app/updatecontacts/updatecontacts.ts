import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { NgForm, FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Contact } from '../contact';
import { ContactService } from '../contact.service';

@Component({
  selector: 'app-updatecontacts',
  standalone: true,
  imports: [CommonModule, HttpClientModule, FormsModule],
  templateUrl: './updatecontacts.html',
  styleUrls: ['./updatecontacts.css'],
  providers: [ContactService]
})
export class Updatecontacts implements OnInit {
  contactID!: number;
  contact: Contact = {
    firstName: '', lastName: '', emailAddress: '',
    phoneNumber: '', status: '', dob: '', imageName: '', typeID: 0
  };

  success = '';
  error = '';
  selectedFile: File | null = null;
  previewUrl: string | null = null;
  originalImageName: string = '';

  constructor(
    private route: ActivatedRoute,
    private contactService: ContactService,
    private router: Router,
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.contactID = +this.route.snapshot.paramMap.get('id')!;
    this.contactService.get(this.contactID).subscribe({
      next: (data: Contact) => {
        this.contact = data;
        this.originalImageName = data.imageName ?? '';
        this.previewUrl = `http://localhost/contactmanagerangular/contactapi/uploads/${this.originalImageName}`;
        this.cdr.detectChanges();
      },
      error: () => this.error = 'Error loading contact.'
    });
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.selectedFile = input.files[0];

      const reader = new FileReader();
      reader.onload = () => {
        this.previewUrl = reader.result as string;
        this.cdr.detectChanges();
      };
      reader.readAsDataURL(this.selectedFile);
    }
  }

  updateContact(form: NgForm) {

     if (!this.contact.phoneNumber) {
    console.log('Phone field is empty or undefined.');
  } else {
    console.log('Phone field has value:', this.contact.phoneNumber);
  }
    if (form.invalid) return;

    const formData = new FormData();
    formData.append('contactID', this.contactID.toString());
    formData.append('firstName', this.contact.firstName ?? '');
    formData.append('lastName', this.contact.lastName ?? '');
    formData.append('emailAddress', this.contact.emailAddress ?? '');
    formData.append('phoneNumber', this.contact.phoneNumber ?? '');
    formData.append('status', this.contact.status ?? '');
    formData.append('dob', this.contact.dob ?? '');
    formData.append('originalImageName', this.originalImageName);

    if (this.selectedFile) {
      formData.append('image', this.selectedFile);
    } else {
      formData.append('imageName', this.contact.imageName ?? '');
    }

    this.http.post('http://localhost/contactmanagerangular/contactapi/edit.php', formData).subscribe({
      next: () => {
        this.success = 'Contact updated successfully';
        this.router.navigate(['/contacts']);
      },
      error: () => this.error = 'Update failed'
    });
  }
}