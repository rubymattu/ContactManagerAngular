import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, NgForm } from '@angular/forms';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Contact } from '../contact';
import { ContactService } from '../contact.service';
import { RouterModule, Router } from '@angular/router';

@Component({
  standalone: true,
  selector: 'app-addcontacts',
  templateUrl: './addcontacts.html',
  styleUrls: ['./addcontacts.css'],
  imports: [CommonModule, FormsModule, HttpClientModule, RouterModule],
  providers: [ContactService]
})
export class Addcontacts implements OnInit {
  contact: Contact = {
    firstName: '', lastName: '', emailAddress: '',
    phoneNumber: '', status: '', dob: '', imageName: '',
    typeID: 0
  };

  selectedFile: File | null = null;
  error = '';
  success = '';
    maxDate: string = '';
  types: { typeID: number, typeName: string }[] = [];

  constructor(
    private contactService: ContactService,
    private http: HttpClient,
    private router: Router,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.loadTypes();
     const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    this.maxDate = `${yyyy}-${mm}-${dd}`;
  }

  loadTypes(): void {
    this.http.get<{ typeID: number, typeName: string }[]>('http://localhost/contactmanagerangular/contactapi/types.php')
    .subscribe({
      next: (data) => {
        this.types = data;
        console.log('Contact types loaded:', this.types);
      },
      error: () => this.error = 'Failed to load contact types'
    });

  }


  addContact(f: NgForm) {
    this.resetAlerts();

    if (!this.contact.imageName) {
      this.contact.imageName = 'placeholder_100.jpg';
    }

    this.contactService.add(this.contact).subscribe(
      (res: Contact) => {
        this.success = 'Successfully created';

        if (this.selectedFile && this.contact.imageName !== 'placeholder_100.jpg') {
          this.uploadFile();
        }

        f.reset();
        this.router.navigate(['/contacts']);
      },
      (err) => {
        this.error = err.error?.message || err.message || 'Error occurred';
        this.cdr.detectChanges();
      }
    );
  }

  uploadFile(): void {
    if (!this.selectedFile) return;

    const formData = new FormData();
    formData.append('image', this.selectedFile);

    this.http.post('http://localhost/contactmanagerangular/contactapi/upload', formData).subscribe(
      response => console.log('File uploaded successfully:', response),
      error => console.error('File upload failed:', error)
    );
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.selectedFile = input.files[0];
      this.contact.imageName = this.selectedFile.name;
    }
  }

  resetAlerts(): void {
    this.error = '';
    this.success = '';
  }
}
