import React, { Component } from 'react'
import Form, { FormProps } from '@hubleto/react-ui/components/fc/Form';
import { useTranslation } from '@hubleto/react-ui/components/fc/TranslatedComponent';
import Table from '@hubleto/react-ui/components/cc/Table';

const FormTeam = (props: FormProps) => {

  return <Form
    {...props}
    componentName='FormTeam'
    model='Hubleto/App/Community/Settings/Models/Team'
    translationContext='Hubleto\\App\\Community\\Settings\\Loader'
    translationContextInner='Components\\FormTeam'
    urlSlug='settings/teams'
    renderTitle={(form: any): any => {
      return <>
        <small>{form.translate('Team')}</small>
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
          {form.renderDivider(form.translate('Team members'))}
          {form.id < 0 ?
            <div className="badge badge-info">{form.translate('First create team, then you will be prompted to add members.')}</div>
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
};

export default FormTeam;