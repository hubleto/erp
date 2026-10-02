import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormAccount, { FormAccountProps } from './FormAccount';

interface TableAccountsProps extends TableProps {
}

const componentName = 'TableAccounts'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/LinkedinMessages';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableAccounts = (props: TableAccountsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Account'}
    endpointParams={{}}
    baseUrlSlug='linkedin-messages/accounts'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormAccount {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableAccounts;
