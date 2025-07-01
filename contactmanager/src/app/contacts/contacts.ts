import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, NgForm } from '@angular/forms';
import { Contact } from '../contact';
import { ContactService } from '../contact.service';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { ChangeDetectorRef } from '@angular/core';

@Component({
  standalone: true,
  selector: 'app-contacts',
  imports: [HttpClientModule, CommonModule, FormsModule],
  providers: [ContactService],
  templateUrl: './contacts.html',
  styleUrls: ['./contacts.css'],  
})
export class Contacts implements OnInit {
  title = 'ContactManager';
  public contacts: Contact[] = [];
  contact: Contact = {firstName:'', lastName:'', emailAddress:'', phone:'', status:'', dob: '', imageName:'', typeID: 0};

  error = '';
  success = '';

  constructor(private contactService: ContactService, private http: HttpClient, private cdr: ChangeDetectorRef) {}

  ngOnInit(): void {
    this.getContacts();
  }

  getContacts(): void {
  this.contactService.getAll().subscribe(
    (contacts: Contact[]) => {
      this.contacts = contacts;
      console.log('Contacts:', this.contacts);
      this.success = 'successful list retrieval';
      this.cdr.detectChanges(); 
    },
    (err) => {
      console.error('Error fetching contacts:', err);
      this.error = 'error retrieving contacts';
    }
  );
}


  resetAlerts(): void {
    this.error = '';
    this.success = '';
  }
}