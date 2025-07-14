import { Routes } from '@angular/router';

import { About } from './about/about';
import { Contacts } from './contacts/contacts';
import { Addcontacts } from './addcontacts/addcontacts';
import { Updatecontacts } from './updatecontacts/updatecontacts';

export const routes: Routes = [
    { path: "contacts", component: Contacts},
    { path: "add", component: Addcontacts},
    { path: "about", component: About},
    { path: "edit/:id", component: Updatecontacts},
    { path: "**", redirectTo: "/contacts"}
];