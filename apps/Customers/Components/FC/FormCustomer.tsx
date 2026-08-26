import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import InputTags from '@hubleto/react-ui/components/fc/Inputs/Tags';
import CalendarTab from '@hubleto/apps/Calendar/Components/FC/CalendarTab';
import CustomerFormActivity from './CustomerFormActivity';
import TableContacts from '@hubleto/apps/Contacts/Components/FC/TableContacts';
import CalendarFormActivity from '@hubleto/apps/Calendar/Components/FC/CalendarFormActivity';

export interface FormCustomerProps extends FormProps {}

const componentName = 'FormCustomer'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Customers';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormCustomerProps) => {
  const form = React.useContext(FormMetaContext);
  const streetLine1: string = useRecordField('street_line_1', '');
  const postalCode: string = useRecordField('postal_code', '');
  const city: string = useRecordField('city', '');
  const region: string = useRecordField('region', '');
  const COUNTRY: any = useRecordField('COUNTRY', {});

  let mapAddress = '';
  if (streetLine1 != '' && city != '' && COUNTRY && COUNTRY.name != '') {
    mapAddress = streetLine1 + ', ' + postalCode + ' ' + city + ', ' + region + ', ' + COUNTRY.name;
  }

  return <div className='flex flex-col md:flex-row gap-2'>
    <div className='flex-2'>
      <Input field='name' customInputProps={{cssClass: 'text-2xl'}} />
      <div className="flex-dyn">
        <div className='grow'>
          <Input field='identifier' />
          <Input field='company_id' />
          <Input field='tax_id' />
          <Input field='vat_id' />
          <Input field='note' customInputProps={{cssClass: 'bg-yellow-50 dark:bg-slate-600'}} />
        </div>
        <div className='grow'>
          <Input field='street_line_1' />
          <Input field='street_line_2' />
          <Input field='postal_code' />
          <Input field='city' />
          <Input field='region' />
          <Input field='id_country' />
          <div className="flex justify-between">
            {mapAddress == '' ? null :
              <div>
                <a
                  href={"https://maps.google.com/?q=" + encodeURIComponent(mapAddress)}
                  target="_blank"
                  className="btn btn-transparent"
                >
                  <span className="icon"><i className="fas fa-map"></i></span>
                  <span className="text">{T.translate("Show on map")}</span>
                </a>
              </div>
            }
          </div>
          <Input field='shared_folder' />
        </div>
      </div>
    </div>
    {form.id > 0 ? <div className='flex-2'>
      <TableContacts
        uid={props.uid + "_table_contacts"}
        parentForm={form}
        showAsCards={true}
        idCustomer={form.id}
      ></TableContacts>
    </div> : null}
  </div>;
}

/** TabCalendar */
const TabCalendar = (props: FormProps) => {
  const form = React.useContext(FormMetaContext);
  return <CalendarTab
    loadEventsEndpoint={'calendar/api/get-calendar-events?calendar=customers&idCustomer=' + form.id}
    logActivityEndpoint={'customers/api/log-activity?idCustomer=' + form.id}
    renderActivityForm={(calendarTab: any) => {
      return <CalendarFormActivity
        calendarTab={calendarTab}
        customInputFields={['id_customer']}
        defaultValues={{id_customer: form.id}}
        model='Hubleto/App/Community/Customers/Models/CustomerActivity'
      ></CalendarFormActivity>;
    }}
  ></CalendarTab>;
}

/** FormCustomer */
const FormCustomer = (props: FormCustomerProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Customer'}
    urlSlug='customers'
    endpointParams={{saveRelations: ['TAGS']}}
    // onAfterFormInitialized={(form: any) => {}}
    renderTopInputs={(form: FormMeta) => {
      const TAGS: Array<any> = useRecordField('TAGS', []);
      return <div className='modal-top-inputs'>
        <Input field='is_active' renderOnlyInputField />
        <Input>
          <InputTags
            field='TAGS'
            value={TAGS}
            model={parentApp + '/Models/Tag'}
            targetColumn='id_customer'
            sourceColumn='id_tag'
            colorColumn='_LOOKUP_COLOR'
            showSelect={false}
            showTagButtons={true}
            editTagsUrl='customers/tags'
            onChange={(input: any, value: any) => {
              form.changeField(input, value);
            }}
            onNewTag={(title: string) => {
              return { id: -1, name: title, color: '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0') }
            }}
          ></InputTags>
        </Input>
        <Input field='id_owner' renderOnlyInputField />
        <Input field='id_manager' renderOnlyInputField />
        <Input field='shared_with' renderOnlyInputField />
      </div>
    }}
    onBeforeSaveRecord={(form: FormMeta, record: any) => {
      if (record.tax_id) record.tax_id = record.tax_id.replace(/\s+/g, "");
      if (record.vat_id) record.vat_id = record.vat_id.replace(/\s+/g, "");
      if (record.company_id) record.company_id = record.company_id.replace(/\s+/g, "");
      return record;
    }}
    title={{fields: ['identifier', 'name'], sub: T.translate('Customer')}}
    tabs={{
      default: {title: <b>{T.translate('Customer')}</b>, content: () => <TabDefault {...props} />},
      calendar: {title: T.translate('Calendar'), content: () => <TabCalendar {...props} />},
    }}
    {...props}
  ></Form>;
}

export default FormCustomer;
