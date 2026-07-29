import React, { Component } from 'react'
import Form, { FormProps } from '@hubleto/react-ui/components/fc/Form';
import { useTranslation } from '@hubleto/react-ui/components/fc/TranslatedComponent';
import Table from '@hubleto/react-ui/components/cc/Table';

const FormTeam = React.memo((props: FormProps) => {
  const { translate } = useTranslation(
    'Hubleto\\App\\Community\\Settings\\Loader',
    'Components\\FormTeam'
  );

  return <Form
    {...props}
    componentName='FormTeam'
    model='Hubleto/App/Community/Settings/Models/Team'
    getRecordFormUrl={(form: any): string => {
      return 'settings/teams/' + (form.record.id > 0 ? form.record.id : 'add');
    }}
    renderTitle={(form: any): any => {
      return <>
        <small>{translate('Team')}</small>
        <h2>{form.record.name ?? '-'}</h2>
      </>;
    }}
    renderContent={(form: any): React.JSX.Element => {
      return <div className='w-full flex gap-2'>
        <div className="p-4 flex-1 text-center">
          <i className="fas fa-users text-primary" style={{fontSize: '8em'}}></i>
        </div>
        <div className="flex-6">
          {form.renderInputWrapper('name')}
          {form.renderInputWrapper('description')}
          {form.renderInputWrapper('id_manager')}
          {form.renderDivider(translate('Team members'))}
          {form.id < 0 ?
            <div className="badge badge-info">{translate('First create team, then you will be prompted to add members.')}</div>
          :
            <Table
              uid='teams_members'
              model='Hubleto/App/Community/Settings/Models/TeamMember'
              customEndpointParams={{idTeam: form.id}}
              itemsPerPage={35}
            ></Table>
          }
        </div>
      </div>;
    }}
  ></Form>;
});

export default FormTeam;