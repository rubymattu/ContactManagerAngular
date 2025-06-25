import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { map } from 'rxjs/operators';
import { Contact } from './contact';

@Injectable({
  providedIn: 'root'
})

export class ContactService {
  baseUrl = 'http://localhost/contactmanagerangular/contactapi';

  constructor(private http: HttpClient) {
    // No statement needed 
  }

  getAll() {
    return this.http.get(`${this.baseUrl}/list`).pipe(
      map((response: any) => {
        return response['data'];
      })
    ); 
  } // retrive all contacts

  add(contact: Contact) {
    return this.http.post(`${this.baseUrl}/add`, {data:contact}).pipe(
      map ((response: any) => {
        return response['data'];
      })
    );
  } // add a new contact

  edit(contact: Contact) {
    return this.http.put(`${this.baseUrl}/edit`, {data:contact});
  } // edit an existing contact

  delete(contactID: number) {
    const params = new HttpParams().set('contactId', contactID.toString());
    return this.http.delete(`${this.baseUrl}/delete`, { params });
  } // delete a contact

} // Contact Service