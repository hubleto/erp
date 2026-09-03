import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormKey, { FormKeyProps } from './FormKey';

interface TableKeysProps extends TableProps {
}

const componentName = 'TableKeys'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Api';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableKeys = (props: TableKeysProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Key'}
    formUrlSlug='api/keys'
    formModalProps={{type: 'right wide'}}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "key") {
        return data.key.substr(0, 8) + ' ...';
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormKey {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableKeys;
