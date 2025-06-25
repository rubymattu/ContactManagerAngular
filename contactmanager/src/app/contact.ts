export interface Contact {
  contactId?: number;
  imageName?: string;
  typeId?: number;
  firstName: string;
  lastName: string;   
  emailAddress?: string;
  phone: string;
  status: string;
  dob: Date;
}