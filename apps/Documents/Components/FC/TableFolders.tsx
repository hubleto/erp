import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';

interface TableFoldersProps extends TableProps {}

const componentName = 'TableFolders'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Documents';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableFolders = (props: TableFoldersProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Folder'}
    baseUrlSlug='documents/folders'
    formModalProps={{type: 'right'}}
    // getRowClassName={(table: TableMeta, rowData: any): string => { return table.getDefaultRowClassName(rowData); }}
    // renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => { return table.renderDefaultCell(columnName, column, data, options); }}
    // renderActionsColumn={(table: TableMeta, row: any) => { return table.renderDefaultActionsColumn(row); }}
    // renderFooter={(table: TableMeta) => { return table.renderDefaultFooter(); }}
    {...props}
  ></Table>
}

export default TableFolders;