# coding: utf-8

guard 'shell' do
  watch(%r{^src/(.*)\.php$}){|m|
    filename = "tests/#{m[1]}Test.php"
    if FileTest.exist?(filename)
      run_phpunit(filename)
    else
      puts "\n>> Not found #{filename}\n"
    end
  }
  watch(%r{^tests/.*/(.*)Test\.php$}) {|m| run_phpunit(m[0]) }
end

def run_phpunit(filename)
  puts "\n>> run: #{filename} @ #{Time.now.strftime("%Y-%m-%d %H:%M:%S")}"
  result = `phpunit --bootstrap tests/bootstrap.php --verbose #{filename}`
  test_result = result.split(/\r\n|\r|\n/).last

  filename = File.basename(filename)
  if test_result =~ /Failures/
    n "#{filename}\n#{test_result}", 'Test failed :('
    puts ">> Failed\n" + result
  elsif test_result =~ /Incomplete/
    n "#{filename}\n#{test_result}", 'Passed (but incomplete tests)'
    puts ">> Passed\n" + result
  else
    n "#{filename}\n#{test_result}", 'Test complete :)'
    puts ">> Passed\n" + result
  end
end