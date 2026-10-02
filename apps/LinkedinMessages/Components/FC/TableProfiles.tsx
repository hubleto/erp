import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormProfile, { FormProfileProps } from './FormProfile';

interface TableProfilesProps extends TableProps {
}

const componentName = 'TableProfiles'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/LinkedinMessages';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableProfiles = (props: TableProfilesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Profile'}
    endpointParams={{}}
    baseUrlSlug='linkedin-messages/profiles'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormProfile {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableProfiles;
