import React, { Component } from 'react'
import Form, { FormProps } from '@hubleto/react-ui/components/fc/Form';
import { useTranslation } from '@hubleto/react-ui/components/fc/TranslatedComponent';

const FormTeam = React.memo((props: FormProps) => {
  const { translate } = useTranslation(
    'Hubleto\\App\\Community\\Api\\Loader',
    'Components\\FormUsage'
  );

  return <Form
    {...props}
    componentName='FormUsage'
    model='Hubleto/App/Community/Api/Models/Usage'
    getRecordFormUrl={(form: any): string => {
      return 'api/usages/' + (form.record.id > 0 ? form.record.id : 'add');
    }}
    renderTitle={(form: any): any => {
      return <>
        <small>{translate('Usage')}</small>
        <h2>Record #{form.record.id ?? '0'}</h2>
      </>;
    }}
    renderTab={(form: any) => {
      switch (form.activeTabUid) {
        case 'default':
          return <>
            {form.renderInputWrapper('id_key')}
            {form.renderInputWrapper('controller')}
            {form.renderInputWrapper('used_on')}
            {form.renderInputWrapper('ip_address')}
            {form.renderInputWrapper('status')}
          </>;
        break;
      }
    }}
  ></Form>;
});

export default FormTeam;