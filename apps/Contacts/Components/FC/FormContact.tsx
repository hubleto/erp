import React, { useEffect } from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import InputTags from '@hubleto/react-ui/components/fc/Inputs/Tags';
import Divider from '@hubleto/react-ui/components/fc/FormComponents/Divider';
import LookupInput from '@hubleto/react-ui/components/fc/Inputs/Lookup';
import VarcharInput from '@hubleto/react-ui/components/fc/Inputs/Varchar';

export interface FormContactProps extends FormProps {}

const componentName = 'FormContact'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Contacts';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormContactProps) => {
  const form = React.useContext(FormMetaContext);
  const TAGS: Array<any> = useRecordField('TAGS');
  const VALUES: Array<any> = useRecordField('VALUES');

  const getType = (value: string) => {
    if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
      return 'email';
    } else if (/^[\+|0-9| ]+$/.test(value)) {
      return 'number';
    } else if (/(https:\/\/www\.|http:\/\/www\.|https:\/\/|http:\/\/)?[a-zA-Z0-9]{2,}(\.[a-zA-Z0-9]{2,})(\.[a-zA-Z0-9]{2,})?/.test(value)) {
      return 'url'
    } else {
      return 'other';
    }
  }

  const getIcon = (value: string) => {
    switch (getType(value)) {
      case 'email': return 'envelope'; break;
      case 'number': return 'phone'; break;
      case 'url': return 'link'; break;
      default: return 'grip-lines'; break;
    }
  }

  return <>
    <div className='card'>
      <div className='card-body flex flex-col md:flex-row gap-2'>
        <div className="flex-3">
          <div className="flex gap-2 w-full">
            <div>
              <i className="fas fa-user text-2xl p-4 text-gray-500"></i>
            </div>
            <div className="w-full">
              <div className='flex gap-2'>
                <Input field='is_primary' renderOnlyInputField customInputProps={{yesText: 'Primary'}} />
                <Input field='is_for_invoicing' renderOnlyInputField customInputProps={{yesText: 'Send invoices'}} />
                <Input field='is_valid' renderOnlyInputField customInputProps={{yesText: 'Valid'}} />
              </div>
              <Input field='salutation' />
              <Input field='title_before' />
              <Input field='first_name' />
              <Input field='middle_name' />
              <Input field='last_name' />
              <Input field='title_after' />
            </div>
          </div>
        </div>
        <div className="flex-1">
          <Input field='id_customer' />
          <Input title={T.translate('Tags')}>
            <InputTags
              field='TAGS'
              value={TAGS}
              model={parentApp + '/Models/Tag'}
              targetColumn='id_contact'
              sourceColumn='id_tag'
              colorColumn='_LOOKUP_COLOR'
              showSelect={false}
              showTagButtons={true}
              onChange={(input: any, value: any) => {
                form.changeField(input, value);
              }}
              onNewTag={(title: string) => {
                return { id: -1, name: title, color: '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0') }
              }}
            ></InputTags>
          </Input>
          <Input field='note' customInputProps={{cssClass: 'bg-yellow-50 dark:bg-slate-600'}} />
        </div>
      </div>
    </div>
    <Divider>{T.translate('Contacts')}</Divider>
    {VALUES ? <div className='flex flex-col gap-2'>{VALUES.map((item, key) => {
      const itemType = getType(item.value);
      return <div key={key} className={'flex gap-2 w-full items-center' + (item._toBeDeleted_ ? ' bg-red-100' : '')}>
        <div className='w-8 text-center'><i className={"fas fa-" + getIcon(item.value)}></i></div>
        <div className='grow'>
          <VarcharInput
            field='VALUES'
            onChange={(input: any, newValue: any) => {
              let newValues = VALUES;
              newValues[key].value = newValue;
              newValues[key].type = getType(newValue);
              form.changeField(input, newValues);
            }}
            value={item.value}
          />
        </div>
        <div>
          <LookupInput
            uid={'value_' + item.id + '_id_category'}
            model='Hubleto/App/Community/Contacts/Models/Category'
            value={item.id_category}
            onChange={(input: any, value: any) => {
              let newValues = VALUES;
              newValues[key].id_category = value;
              form.changeRecord({ VALUES: newValues });
            }}
          ></LookupInput>
        </div>
        <button
          className={'btn ' + (item._toBeDeleted_ ? 'btn-primary' : 'btn-danger')}
          onClick={() => {
            let newValues = VALUES;
            if (newValues[key].id < 0) {
              newValues.splice(Number(key), 1);
            } else {
              newValues[key]._toBeDeleted_ = !newValues[key]._toBeDeleted_;
            }
            form.changeRecord({ VALUES: newValues });
          }}
        >
          <span className="icon"><i className={'fas ' + (item._toBeDeleted_ ? 'fa-times' : 'fa-trash-can')}></i></span>
        </button>
      </div>;
    })}</div> : null}
    <a
      className="btn btn-add-outline mt-2"
      onClick={() => {
        let newValues: Array<any> = VALUES || [];
        newValues.push({
          id: -1,
          id_contact: { _useMasterRecordId_: true },
          type: 'email',
        });
        form.changeRecord({VALUES: newValues});
      }}
    >
      <span className="icon"><i className="fas fa-add"></i></span>
      <span className="text">{T.translate('Add contact')}</span>
    </a>
  </>;
}

/** FormContact */
const FormContact = (props: FormContactProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Contact'}
    urlSlug='contacts'
    endpointParams={{saveRelations: ['VALUES', 'TAGS']}}
    renderTitle={(): React.JSX.Element => {
      const firstName = useRecordField('first_name', '');
      const lastName = useRecordField('last_name', '');

      return <div>
        <h2>{firstName}&nbsp;{lastName}</h2>
        <small>{T.translate('Contact')}</small>
      </div>;
    }}

    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormContact;
