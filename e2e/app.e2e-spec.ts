import { PizzeriaPage } from './app.po';

describe('pizzeria App', () => {
  let page: PizzeriaPage;

  beforeEach(() => {
    page = new PizzeriaPage();
  });

  it('should display welcome message', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('Welcome to app!!');
  });
});
